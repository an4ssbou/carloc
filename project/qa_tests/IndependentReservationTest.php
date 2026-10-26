<?php

namespace Tests\Independent;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndependentReservationTest extends TestCase
{
    use RefreshDatabase;

    private function reserve(int $days = 2): Reservation
    {
        $client = User::factory()->create([
            'cin_photo' => 'synthetic/cin.txt', 'license_photo' => 'synthetic/license.txt',
            'first_name' => '<script>alert(1)</script>',
        ]);
        $vehicle = Vehicle::factory()->create(['price_month' => 6000]);
        $response = $this->actingAs($client)->postJson('/reservation', [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(4 + $days)->toDateString(),
        ])->assertOk();
        return Reservation::findOrFail($response->json('reservation.id'));
    }

    public function test_thirty_days_are_accepted_and_contract_escapes_snapshot(): void
    {
        $reservation = $this->reserve(30);
        $this->assertSame(30, $reservation->duration_days);
        $this->assertSame('6000.00', $reservation->total_price);
        $this->get('/reservation/'.$reservation->id.'/contract')->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_terminal_reservation_cannot_be_paid_or_cancelled(): void
    {
        $reservation = $this->reserve();
        $reservation->update(['status' => 'completed']);
        $this->postJson('/reservation/'.$reservation->id.'/simulate-payment')->assertUnprocessable();
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertSessionHas('error');
        $this->assertSame('completed', $reservation->fresh()->status);
        $this->assertNull($reservation->fresh()->payment_reference);
    }

    public function test_one_second_before_deadline_accepts_cancellation(): void
    {
        $this->travelTo(now()->startOfSecond());
        $reservation = $this->reserve();
        $this->travelTo($reservation->created_at->copy()->addHours(24)->subSecond());
        $this->post('/reservation/'.$reservation->id.'/cancel')->assertRedirect();
        $this->assertSame('cancelled', $reservation->fresh()->status);
    }

    public function test_registration_uses_actual_first_last_name_and_phone_contract(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->post('/register', [
            'first_name' => 'Client', 'last_name' => 'Synthetique',
            'email' => 'independent@carloc.test', 'phone' => '0612345678',
            'password' => 'Demo-Local-2026!', 'password_confirmation' => 'Demo-Local-2026!',
        ])->assertSessionHasNoErrors()->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'independent@carloc.test', 'role' => 'client']);
    }

    public function test_profile_actual_contract_preserves_email_verification_when_email_unchanged(): void
    {
        $client = User::factory()->create(['first_name' => 'Client', 'last_name' => 'Demo']);
        $this->actingAs($client)->patch('/profile', [
            'first_name' => 'Client', 'last_name' => 'Updated',
            'email' => $client->email, 'phone' => $client->phone,
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');
        $this->assertSame('Updated', $client->fresh()->last_name);
        $this->assertNotNull($client->fresh()->email_verified_at);
    }

    public function test_account_deletion_soft_deletes_and_logs_out(): void
    {
        $client = User::factory()->create();
        $this->actingAs($client)->delete('/profile', ['password' => 'password'])
            ->assertSessionHasNoErrors()->assertRedirect('/');
        $this->assertGuest();
        $this->assertSoftDeleted('users', ['id' => $client->id]);
    }
}
