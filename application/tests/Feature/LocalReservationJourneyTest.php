<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LocalReservationJourneyTest extends TestCase
{
    use RefreshDatabase;

    private function client(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'cin_photo' => 'synthetic/cin.txt',
            'license_photo' => 'synthetic/license.txt',
        ], $attributes));
    }

    private function vehicle(array $attributes = []): Vehicle
    {
        return Vehicle::factory()->create(array_merge([
            'price_per_day' => 300, 'price_week' => 1800,
            'price_two_weeks' => 3300, 'price_month' => 6000,
        ], $attributes));
    }

    private function payload(Vehicle $vehicle, int $days = 2): array
    {
        return [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(3 + $days)->toDateString(),
        ];
    }

    private function reserve(User $client, Vehicle $vehicle, int $days = 2): Reservation
    {
        $response = $this->actingAs($client)->postJson('/reservation', $this->payload($vehicle, $days));
        $response->assertOk()->assertJsonPath('success', true);

        return Reservation::findOrFail($response->json('reservation.id'));
    }

    public function test_booking_and_successful_cancellation_keep_existing_notifications_without_duplicates(): void
    {
        config(['mail.default' => 'array']);
        $client = $this->client();
        $vehicle = $this->vehicle();
        $founder = $vehicle->agency->founder;
        $reservation = $this->reserve($client, $vehicle);
        $this->assertSame(1, $client->notifications()->count());
        $this->assertSame(1, $founder->notifications()->count());
        $this->postJson('/reservation', $this->payload($vehicle))->assertUnprocessable();
        $this->assertSame(2, DB::table('notifications')->count());
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertRedirect();
        $this->assertSame(2, $founder->notifications()->count());
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertSessionHas('error');
        $this->assertSame(3, DB::table('notifications')->count());
        $messages = DB::table('notifications')->pluck('data')->implode(' ');
        $this->assertStringNotContainsString($client->cin_number, $messages);
    }

    public function test_guest_can_browse_catalog_and_login_masks_password_without_javascript(): void
    {
        $this->get('/vehicles')->assertOk();
        $this->get('/login')->assertOk()->assertSee('type="password"', false);
    }

    public function test_invalid_dates_are_422_without_a_reservation(): void
    {
        $vehicle = $this->vehicle();
        $this->actingAs($this->client())->postJson('/reservation', [
            'vehicle_id' => $vehicle->id, 'start_date' => 'wrong', 'end_date' => 'wrong',
        ])->assertUnprocessable()->assertJsonValidationErrors('start_date');
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_guest_unverified_and_incomplete_profiles_cannot_reserve(): void
    {
        $vehicle = $this->vehicle();
        $this->get('/reservation/'.$vehicle->uuid)->assertRedirect('/login');
        $this->postJson('/reservation', $this->payload($vehicle))->assertUnauthorized();
        $this->actingAs($this->client(['email_verified_at' => null]))
            ->postJson('/reservation', $this->payload($vehicle))->assertForbidden();
        $this->actingAs($this->client(['license_photo' => null]))
            ->postJson('/reservation', $this->payload($vehicle))->assertUnprocessable();
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_inactive_account_cannot_reserve_even_by_direct_request(): void
    {
        $vehicle = $this->vehicle();
        $this->actingAs($this->client(['is_active' => false]))
            ->postJson('/reservation', $this->payload($vehicle))->assertForbidden();
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_suspended_agency_and_manual_unavailability_are_rejected(): void
    {
        $vehicle = $this->vehicle();
        $vehicle->agency->update(['status' => 'suspended']);
        $this->actingAs($this->client())->postJson('/reservation', $this->payload($vehicle))->assertUnprocessable();
        $vehicle->agency->update(['status' => 'active']);
        $vehicle->update(['is_manually_unavailable' => true]);
        $this->postJson('/reservation', $this->payload($vehicle))->assertUnprocessable();
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_server_price_and_identity_override_injected_values_and_snapshot_is_immutable(): void
    {
        $client = $this->client(['first_name' => 'Client', 'last_name' => 'Demo']);
        $vehicle = $this->vehicle();
        $response = $this->actingAs($client)->postJson('/reservation', array_merge($this->payload($vehicle, 7), [
            'amount' => 1, 'total_price' => 1, 'user_id' => 999999, 'agency_id' => 999999,
        ]));
        $response->assertOk();
        $reservation = Reservation::findOrFail($response->json('reservation.id'));
        $this->assertSame('1800.00', $reservation->total_price);
        $this->assertSame($client->id, $reservation->user_id);
        $this->assertSame($vehicle->agency_id, $reservation->agency_id);
        $client->update(['first_name' => 'Changed']);
        $vehicle->update(['price_per_day' => 999]);
        $this->get('/reservation/'.$reservation->id.'/contract')
            ->assertOk()->assertSee('Client Demo')->assertDontSee('Changed')->assertSee('1 800,00');
    }

    public function test_a_globally_busy_vehicle_can_be_reserved_for_a_free_future_period(): void
    {
        $vehicle = $this->vehicle(['status' => 'unavailable']);
        $this->reserve($this->client(), $vehicle);
        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_overlap_is_refused_and_cancellation_releases_the_period(): void
    {
        $client = $this->client();
        $vehicle = $this->vehicle();
        $reservation = $this->reserve($client, $vehicle);
        $this->postJson('/reservation', $this->payload($vehicle))->assertUnprocessable();
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertRedirect();
        $this->reserve($client, $vehicle);
        $this->assertDatabaseCount('reservations', 2);
    }

    public function test_invalid_durations_and_missing_vehicle_do_not_create_rows(): void
    {
        $vehicle = $this->vehicle();
        $this->actingAs($this->client());
        foreach ([0, 31] as $days) {
            $this->postJson('/reservation', $this->payload($vehicle, $days))->assertUnprocessable();
        }
        $this->postJson('/reservation', array_merge($this->payload($vehicle), ['start_date' => now()->subDay()->toDateString()]))->assertUnprocessable();
        $this->postJson('/reservation', array_merge($this->payload($vehicle), ['vehicle_id' => 999999]))->assertUnprocessable();
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_simulation_is_persisted_and_idempotent_without_using_client_amount(): void
    {
        $reservation = $this->reserve($this->client(), $this->vehicle());
        $url = '/reservation/'.$reservation->id.'/simulate-payment';
        $this->postJson($url, ['amount' => 1])->assertOk()->assertJsonPath('simulated', true);
        $reservation->refresh();
        $reference = $reservation->payment_reference;
        $this->assertNotEmpty($reference);
        $this->assertSame('simulated', $reservation->payment_status);
        $this->assertSame('600.00', $reservation->total_price);
        $this->postJson($url)->assertOk();
        $this->assertSame($reference, $reservation->fresh()->payment_reference);
        $this->get('/reservation/'.$reservation->id.'/show')->assertOk()->assertSee('Paiement simulé')->assertSee('aucun débit');
    }

    public function test_third_parties_cannot_view_contract_pay_or_cancel(): void
    {
        $reservation = $this->reserve($this->client(), $this->vehicle());
        $this->actingAs($this->client());
        foreach (['show', 'contract'] as $suffix) {
            $this->get('/reservation/'.$reservation->id.'/'.$suffix)->assertForbidden();
        }
        $this->postJson('/reservation/'.$reservation->id.'/simulate-payment')->assertForbidden();
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertForbidden();
        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_cancellation_at_exact_deadline_cancels_simulation_and_preserves_contract(): void
    {
        $this->travelTo(now()->startOfSecond());
        $reservation = $this->reserve($this->client(), $this->vehicle());
        $this->postJson('/reservation/'.$reservation->id.'/simulate-payment')->assertOk();
        $this->travelTo($reservation->created_at->copy()->addHours(24));
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertRedirect();
        $reservation->refresh();
        $this->assertSame('cancelled', $reservation->status);
        $this->assertSame('simulation_cancelled', $reservation->payment_status);
        $this->assertNotNull($reservation->cancelled_at);
        $this->postJson('/reservation/'.$reservation->id.'/simulate-payment')->assertUnprocessable();
        $this->get('/reservation/'.$reservation->id.'/contract')->assertOk()->assertSee('Annulée');
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertSessionHas('error');
    }

    public function test_cancellation_after_deadline_is_refused(): void
    {
        $reservation = $this->reserve($this->client(), $this->vehicle());
        $this->travelTo($reservation->created_at->copy()->addHours(24)->addSecond());
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertSessionHas('error');
        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_inactive_client_cannot_pay_cancel_or_read_private_reservation(): void
    {
        $client = $this->client();
        $reservation = $this->reserve($client, $this->vehicle());
        $client->update(['is_active' => false]);
        $this->actingAs($client);
        $this->get('/reservation/'.$reservation->id.'/contract')->assertForbidden();
        $this->assertGuest();
        $this->actingAs($client);
        $this->postJson('/reservation/'.$reservation->id.'/simulate-payment')->assertForbidden();
        $this->assertGuest();
        $this->actingAs($client);
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertForbidden();
        $this->assertGuest();
    }

    public function test_demo_seed_is_repeatable_and_keeps_synthetic_accounts_and_vehicle(): void
    {
        $this->seed(DemoSeeder::class);
        $this->seed(DemoSeeder::class);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('agencies', 1);
        $this->assertDatabaseCount('vehicles', 1);
        $client = User::where('email', 'demo@carloc.test')->firstOrFail();
        $this->assertTrue($client->hasVerifiedEmail());
        $this->assertTrue(Hash::check('Demo-Local-2026!', $client->password));
        $this->assertSame('active', Agency::firstOrFail()->status);
        $client->update(['is_active' => false]);
        Agency::firstOrFail()->update(['status' => 'suspended']);
        Vehicle::firstOrFail()->update(['is_manually_unavailable' => true]);
        $this->seed(DemoSeeder::class);
        $this->assertFalse($client->fresh()->is_active);
        $this->assertSame('suspended', Agency::firstOrFail()->status);
        $this->assertTrue(Vehicle::firstOrFail()->is_manually_unavailable);
    }

    public function test_legacy_payment_cannot_charge_and_form_does_not_collect_card_details(): void
    {
        $vehicle = $this->vehicle();
        $this->actingAs($this->client())->postJson('/payment', ['amount' => 1, 'stripeToken' => 'token'])
            ->assertStatus(409)->assertJsonPath('success', false);
        $this->get('/reservation/'.$vehicle->uuid)->assertOk()
            ->assertSee('Simulation locale')->assertDontSee('card-number')->assertDontSee('card-cvc');
    }
}
