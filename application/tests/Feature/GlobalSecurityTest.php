<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\User;
use App\Notifications\AppNotification;
use App\Support\SafeNotifier;
use Database\Seeders\AgencySeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ReservationSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\TestHandler;
use Tests\TestCase;

class GlobalSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_account_cannot_start_a_session(): void
    {
        $user = User::factory()->inactive()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_disabling_an_already_authenticated_account_denies_private_access(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $user->update(['is_active' => false]);
        $this->getJson('/profile')->assertForbidden();
        $this->assertGuest();
    }

    public function test_disabled_super_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->superAdmin()->inactive()->create();
        $this->actingAs($user)->getJson('/admin/dashboard')->assertForbidden();
    }

    public function test_identity_documents_are_private_streamed_and_not_cacheable(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('cin_photos/synthetic.jpg', 'synthetic-private-document');
        $user = User::factory()->create(['cin_photo' => 'cin_photos/synthetic.jpg']);
        $this->get(route('profile.cin_photo'))->assertRedirect('/login');
        $this->actingAs($user)->get(route('profile.cin_photo'))
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertStreamedContent('synthetic-private-document');
    }

    public function test_profile_and_public_pages_have_defensive_headers_and_private_pages_are_not_cached(): void
    {
        $this->get('/login')->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $user = User::factory()->create();
        $this->actingAs($user)->get('/profile')->assertOk()->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_avatar_fallback_does_not_disclose_names_to_a_third_party_and_identity_paths_are_hidden(): void
    {
        $user = User::factory()->create(['first_name' => 'SyntheticSecret', 'cin_photo' => 'cin_photos/private.jpg', 'license_photo' => 'licenses/private.jpg']);
        $this->assertStringNotContainsString('ui-avatars.com', $user->profile_photo_url);
        $this->assertStringNotContainsString('SyntheticSecret', $user->profile_photo_url);
        foreach (['cin_number', 'cin_photo', 'license_photo'] as $field) {
            $this->assertArrayNotHasKey($field, $user->toArray());
        }
    }

    public function test_agency_forms_work_with_cold_city_cache(): void
    {
        Cache::forget('ma_cities');
        $this->actingAs(User::factory()->create())->get('/agency/create')->assertOk();
        $owner = User::factory()->agencyOwner()->create();
        $agency = Agency::factory()->create(['founder_id' => $owner->id]);
        Cache::forget('ma_cities');
        $this->actingAs($owner)->get('/agency/edit/'.$agency->slug)->assertOk();
        $this->actingAs(User::factory()->superAdmin()->create())->get('/admin/agencies/'.$agency->slug.'/edit')->assertOk();
    }

    public function test_sensitive_auth_endpoints_bound_repeated_invalid_attempts(): void
    {
        foreach (['/register', '/forgot-password', '/reset-password'] as $path) {
            for ($attempt = 0; $attempt < 5; $attempt++) {
                $this->postJson($path, [])->assertUnprocessable();
            }
            $this->postJson($path, [])->assertStatus(429);
            $this->travel(61)->seconds();
        }
        $this->actingAs(User::factory()->create());
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/confirm-password', ['password' => 'wrong'])->assertUnprocessable();
        }
        $this->postJson('/confirm-password', ['password' => 'wrong'])->assertStatus(429);
    }

    public function test_password_reset_response_does_not_reveal_account_existence(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $response = $this->post('/forgot-password', ['email' => $user->email]);
        $response->assertSessionHasNoErrors();
        $message = session('status');
        $this->post('/forgot-password', ['email' => 'absent-synthetic@carloc.test'])
            ->assertSessionHasNoErrors()->assertSessionHas('status', $message);
    }

    public function test_notification_delivery_failure_does_not_log_message_content(): void
    {
        Log::getLogger()->pushHandler(new TestHandler);
        $records = [];
        Log::listen(function ($event) use (&$records) {
            $records[] = [$event->message, $event->context];
        });
        $target = new class
        {
            public function notify($notification): void
            {
                throw new \RuntimeException('SYNTHETIC-PRIVATE-MAIL-CONTENT');
            }
        };
        SafeNotifier::send($target, new AppNotification(['subject' => 'Test', 'body' => 'Synthetic']), ['event' => 'delivery_test']);
        $this->assertNotEmpty($records);
        $this->assertStringNotContainsString('SYNTHETIC-PRIVATE-MAIL-CONTENT', json_encode($records));
    }

    public function test_agency_dashboard_executes_only_local_scripts(): void
    {
        $owner = User::factory()->agencyOwner()->create();
        $agency = Agency::factory()->create(['founder_id' => $owner->id]);
        $response = $this->actingAs($owner)->get('/agency/'.$agency->slug.'/dashboard')->assertOk();
        preg_match_all('/<script[^>]+src="([^"]+)"/', $response->getContent(), $matches);
        $this->assertNotEmpty($matches[1]);
        foreach ($matches[1] as $source) {
            $this->assertContains(parse_url(html_entity_decode($source), PHP_URL_HOST), [null, 'localhost', '127.0.0.1']);
        }
    }

    public function test_a_changed_password_invalidates_an_existing_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/profile')->assertOk();
        $user->update(['password' => 'Changed-Local-2026!']);
        $this->getJson('/profile')->assertUnauthorized();
        $this->assertGuest();
    }

    public function test_an_existing_strict_csp_is_preserved(): void
    {
        Route::get('/qa/strict-csp', fn () => response('synthetic')->header('Content-Security-Policy', "default-src 'none'; sandbox"));
        $this->get('/qa/strict-csp')->assertOk()->assertHeader('Content-Security-Policy', "default-src 'none'; sandbox");
    }

    public function test_example_seeders_cannot_create_known_accounts_in_production(): void
    {
        $this->app['env'] = 'production';
        foreach ([DatabaseSeeder::class, SuperAdminSeeder::class,
            AgencySeeder::class, ClientSeeder::class, ReservationSeeder::class] as $class) {
            try {
                app($class)->run();
                $this->fail($class.' must refuse production');
            } catch (\RuntimeException $exception) {
                $this->assertStringContainsString('local/testing', $exception->getMessage());
            }
            $this->assertDatabaseCount('users', 0);
            $this->assertDatabaseCount('agencies', 0);
            $this->assertDatabaseCount('reservations', 0);
        }
    }
}
