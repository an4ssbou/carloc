<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Models\Agency;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalReservationRaceTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $owner = User::factory()->agencyOwner()->create();
        $agency = Agency::factory()->create(['founder_id' => $owner->id]);
        $vehicle = Vehicle::factory()->create(['agency_id' => $agency->id]);
        $reservation = Reservation::create(['vehicle_id' => $vehicle->id, 'agency_id' => $agency->id,
            'user_id' => User::factory()->create()->id, 'start_date' => now()->addDays(3), 'end_date' => now()->addDays(5),
            'status' => 'pending', 'price_applied' => 300, 'total_price' => 600, 'payment_status' => 'simulated']);

        return [$owner, $agency, $reservation];
    }

    public function test_agency_and_admin_cannot_revive_cancellation_that_happened_after_route_binding(): void
    {
        foreach (['agency', 'admin'] as $actor) {
            [$owner, $agency, $reservation] = $this->fixture();
            $boundBeforeCancellation = Reservation::findOrFail($reservation->id);
            $reservation->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancelled_by' => 'client']);
            $this->actingAs($actor === 'agency' ? $owner : User::factory()->superAdmin()->create());
            if ($actor === 'agency') {
                app(DashboardController::class)->confirmReservation($agency->slug, $boundBeforeCancellation);
            } else {
                app(AdminDashboardController::class)->confirmReservation($boundBeforeCancellation);
            }
            $this->assertSame('cancelled', $reservation->fresh()->status);
            $this->assertSame('simulation_cancelled', $reservation->fresh()->payment_status);
        }
    }

    public function test_agency_and_admin_cannot_cancel_completion_that_happened_after_route_binding(): void
    {
        foreach (['agency', 'admin'] as $actor) {
            [$owner, $agency, $reservation] = $this->fixture();
            $reservation->update(['status' => 'confirmed']);
            $boundBeforeCompletion = Reservation::findOrFail($reservation->id);
            $reservation->update(['status' => 'completed', 'completed_at' => now()]);
            $this->actingAs($actor === 'agency' ? $owner : User::factory()->superAdmin()->create());
            if ($actor === 'agency') {
                app(DashboardController::class)->cancelReservation($agency->slug, $boundBeforeCompletion);
            } else {
                app(AdminDashboardController::class)->cancelReservation($boundBeforeCompletion);
            }
            $this->assertSame('completed', $reservation->fresh()->status);
        }
    }
}
