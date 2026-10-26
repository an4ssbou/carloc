<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowcaseDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_revenue_buckets_use_rental_start_date_and_include_days_29_to_31(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 25)->startOfDay()->addHours(12));
        $owner = User::factory()->agencyOwner()->create();
        $agency = Agency::factory()->create(['founder_id' => $owner->id]);
        $car = Vehicle::factory()->create(['agency_id' => $agency->id]);
        $client = User::factory()->create();
        foreach ([['2026-10-30', '2026-11-02', '2026-09-20', 900, 'confirmed'], ['2026-09-29', '2026-10-01', '2026-09-15', 600, 'completed']] as $i => [$start, $end, $created, $total, $status]) {
            Reservation::createQuietly(['reference' => 'SHOW-CHART-'.$i, 'vehicle_id' => $car->id, 'agency_id' => $agency->id,
                'user_id' => $client->id, 'start_date' => $start, 'end_date' => $end, 'duration_days' => $i ? 2 : 3,
                'price_applied' => 300, 'total_price' => $total, 'status' => $status, 'created_at' => $created, 'updated_at' => $created]);
        }
        $this->actingAs($owner)->get('/agency/'.$agency->slug.'/dashboard')->assertOk()
            ->assertViewHas('revenueThisMonth', fn ($rows) => count($rows) === 5 && (float) $rows[4] === 900.0 && array_sum($rows) === 900.0)
            ->assertViewHas('revenueLastMonth', fn ($rows) => count($rows) === 5 && (float) $rows[4] === 600.0 && array_sum($rows) === 600.0);
    }
}
