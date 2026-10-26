<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalAdminPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_agencies_are_paginated_without_losing_global_totals(): void
    {
        $admin = User::factory()->superAdmin()->create();
        for ($i = 0; $i < 25; $i++) {
            Agency::create(['name' => 'Synthetic Agency '.$i, 'city' => 'Rabat', 'founder_id' => $admin->id,
                'status' => $i < 20 ? 'active' : 'pending']);
        }
        $this->actingAs($admin)->get('/admin/agencies?status=active&page=2')->assertOk()
            ->assertViewHas('totalAgencies', 25)->assertViewHas('activeAgenciesCount', 20)
            ->assertViewHas('pendingAgenciesCount', 5)
            ->assertViewHas('agencies', fn ($rows) => $rows instanceof LengthAwarePaginator
                && $rows->currentPage() === 2 && $rows->count() === 8 && $rows->total() === 20)
            ->assertSee('status=active', false);
    }

    public function test_admin_user_totals_do_not_hydrate_the_entire_user_table(): void
    {
        $admin = User::factory()->superAdmin()->create();
        User::factory()->count(35)->create();
        User::factory()->count(5)->inactive()->create();
        $hydrated = 0;
        User::retrieved(function () use (&$hydrated) {
            $hydrated++;
        });
        $this->actingAs($admin)->get('/admin/users?role=client')->assertOk()
            ->assertViewHas('totalUsers', 41)->assertViewHas('activeUsersCount', 36)
            ->assertViewHas('clientsCount', 40)->assertViewHas('verifiedUsersCount', 41);
        $this->assertLessThanOrEqual(12, $hydrated, 'Admin user statistics must not load every private user record.');
        fwrite(STDOUT, "PERF admin_users hydrated={$hydrated} for 41 records\n");
    }

    public function test_admin_vehicle_and_reservation_totals_do_not_hydrate_entire_tables(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $agency = Agency::factory()->create();
        $vehicle = Vehicle::factory()->create(['agency_id' => $agency->id]);
        Vehicle::factory()->count(20)->create(['agency_id' => $agency->id]);
        for ($i = 0; $i < 25; $i++) {
            Reservation::create(['vehicle_id' => $vehicle->id, 'agency_id' => $agency->id, 'user_id' => $admin->id,
                'start_date' => now()->addDays(2)->toDateString(), 'end_date' => now()->addDays(4)->toDateString(),
                'price_applied' => 300, 'total_price' => 600, 'status' => 'pending']);
        }
        $vehicles = $reservations = 0;
        Vehicle::retrieved(function () use (&$vehicles) {
            $vehicles++;
        });
        Reservation::retrieved(function () use (&$reservations) {
            $reservations++;
        });
        $this->actingAs($admin)->get('/admin/vehicles')->assertOk()->assertViewHas('totalVehicles', 21);
        $this->assertLessThanOrEqual(13, $vehicles);
        fwrite(STDOUT, "PERF admin_vehicles hydrated={$vehicles} for 21 records\n");
        $vehicles = 0;
        $this->get('/admin/reservations')->assertOk()->assertViewHas('totalReservations', 25);
        $this->assertLessThanOrEqual(13, $reservations);
        fwrite(STDOUT, "PERF admin_reservations hydrated={$reservations} for 25 records\n");
    }
}
