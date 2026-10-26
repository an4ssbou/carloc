<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Database\Seeders\DemoSeeder;
use Database\Seeders\ShowcaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShowcaseSeederTest extends TestCase
{
    use RefreshDatabase;

    private function prepare(): void
    {
        $this->assertTrue(class_exists(ShowcaseSeeder::class), 'The optional showcase seeder must exist.');
        $this->travelTo(now()->setDate(2026, 10, 25)->startOfDay()->addHours(12));
        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        Notification::fake();
        $this->seed(DemoSeeder::class);
    }

    public function test_showcase_is_rich_repeatable_and_preserves_previous_records(): void
    {
        $this->prepare();
        $sentinel = User::factory()->create(['first_name' => 'Untouched']);
        $original = Vehicle::where('registration_number', 'DEMO-001')->firstOrFail();
        $before = $original->getAttributes();
        $this->seed(ShowcaseSeeder::class);
        $this->assertDatabaseCount('vehicles', 25);
        $this->assertDatabaseCount('agencies', 4);
        $this->assertSame(40, User::where('email', 'like', 'client-%@showcase.carloc.test')->count());
        $this->assertSame(180, Reservation::where('reference', 'like', 'SHOW-RES-%')->count());
        $this->assertDatabaseCount('demo_datasets', 1);
        $this->assertSame($before, $original->fresh()->getAttributes());
        $vehicle = Vehicle::where('registration_number', 'SHOW-001')->firstOrFail();
        $vehicle->updateQuietly(['price_per_day' => 777]);
        $counts = [User::count(), Vehicle::count(), Reservation::count(), VehiclePhoto::count()];
        $ids = Reservation::where('reference', 'like', 'SHOW-RES-%')->orderBy('id')->pluck('id')->all();
        $this->seed(ShowcaseSeeder::class);
        $this->assertSame($counts, [User::count(), Vehicle::count(), Reservation::count(), VehiclePhoto::count()]);
        $this->assertSame($ids, Reservation::where('reference', 'like', 'SHOW-RES-%')->orderBy('id')->pluck('id')->all());
        $this->assertSame('777.00', $vehicle->fresh()->price_per_day);
        $this->assertSame('Untouched', $sentinel->fresh()->first_name);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_assets_match_registered_models_and_all_seeded_photos_exist(): void
    {
        $this->prepare();
        $this->seed(ShowcaseSeeder::class);
        $assets = collect(json_decode(file_get_contents(resource_path('demo/vehicles.json')), true))->keyBy('key');
        $this->assertCount(12, $assets);
        foreach (Vehicle::where('registration_number', 'like', 'SHOW-%')->with('primaryPhoto')->get() as $vehicle) {
            $this->assertNotNull($vehicle->primaryPhoto);
            $key = pathinfo($vehicle->primaryPhoto->path, PATHINFO_FILENAME);
            $this->assertTrue($assets->has($key));
            $asset = $assets[$key];
            $this->assertSame($asset['brand'], $vehicle->brand);
            $this->assertSame($asset['model'], $vehicle->model);
            $this->assertSame('CC0', $asset['license']);
            $this->assertStringStartsWith('https://commons.wikimedia.org/', $asset['source_page']);
            $this->assertNotEmpty($asset['author']);
            $this->assertSame($asset['sha256'], hash_file('sha256', public_path($asset['asset'])));
            Storage::disk('public')->assertExists($vehicle->primaryPhoto->path);
        }
    }

    public function test_six_months_have_valid_prices_dates_and_nonoverlapping_active_reservations(): void
    {
        $this->prepare();
        $this->seed(ShowcaseSeeder::class);
        $rows = Reservation::where('reference', 'like', 'SHOW-RES-%')->with('vehicle')->get();
        $months = $rows->whereIn('status', ['confirmed', 'completed'])->groupBy(fn ($r) => $r->start_date->format('Y-m'));
        $this->assertCount(6, $months);
        foreach ($months as $month) {
            $this->assertGreaterThan(0, $month->sum('total_price'));
        }
        foreach ($rows as $row) {
            $days = (int) $row->start_date->diffInDays($row->end_date);
            $this->assertSame($days, $row->duration_days);
            $this->assertEqualsWithDelta($row->vehicle->calculateRentalTotal($days), (float) $row->total_price, .01);
            $this->assertTrue($row->created_at->lte($row->start_date->endOfDay()));
            if ($row->status === 'completed') {
                $this->assertTrue($row->end_date->lt(now()->startOfDay()));
            }
            if ($row->status === 'pending') {
                $this->assertNull($row->confirmed_at);
            }
            if ($row->status === 'cancelled') {
                $this->assertNotNull($row->cancelled_at);
                $this->assertNotEmpty($row->cancelled_by);
            }
        }
        $overlaps = DB::selectOne("SELECT COUNT(*) as n FROM reservations a JOIN reservations b ON a.vehicle_id=b.vehicle_id AND a.id<b.id AND a.start_date<=b.end_date AND b.start_date<=a.end_date WHERE a.status IN ('pending','confirmed') AND b.status IN ('pending','confirmed')");
        $this->assertSame(0, (int) $overlaps->n);
    }

    public function test_production_refusal_happens_before_any_write(): void
    {
        $this->assertTrue(class_exists(ShowcaseSeeder::class));
        Storage::fake('local');
        Storage::fake('public');
        $this->app['env'] = 'production';
        $refused = false;
        try {
            app(ShowcaseSeeder::class)->run();
        } catch (\RuntimeException $e) {
            $refused = str_contains($e->getMessage(), 'local/testing');
        }
        $this->assertTrue($refused);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('vehicles', 0);
        $this->assertDatabaseCount('demo_datasets', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_demo_disclosure_is_visible_locally_and_absent_in_production(): void
    {
        $this->prepare();
        $this->seed(ShowcaseSeeder::class);
        $this->app['env'] = 'local';
        $this->get('/vehicles')->assertOk()->assertSee('DÉMONSTRATION')->assertSee('photos illustratives');
        $this->app['env'] = 'production';
        $this->get('/vehicles')->assertOk()->assertDontSee('data-showcase-notice', false);
    }

    public function test_legacy_synthetic_sandero_colour_matches_its_first_photo(): void
    {
        $this->prepare();
        $car = Vehicle::where('registration_number', 'DEMO-001')->firstOrFail();
        $car->updateQuietly(['color' => 'Blanc']);
        $car->forceFill(['has_gps' => true, 'gps_latitude' => 33.5731, 'gps_longitude' => -7.5898,
            'gps_recorded_at' => now(), 'gps_received_at' => now(), 'gps_source' => 'simulation'])->saveQuietly();
        $before = $car->fresh()->getAttributes();
        $this->seed(ShowcaseSeeder::class);
        $this->assertSame('Rouge', $car->fresh()->color);
        $before['color'] = 'Rouge';
        $this->assertSame($before, $car->fresh()->getAttributes());
        $this->assertSame('showcase/vehicles/sandero.jpg', $car->fresh()->primaryPhoto->path);
        $car->updateQuietly(['color' => 'Bleu personnalisé']);
        $this->seed(ShowcaseSeeder::class);
        $this->assertSame('Bleu personnalisé', $car->fresh()->color);
    }
}
