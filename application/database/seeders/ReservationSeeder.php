<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Les données d’exemple sont réservées à local/testing.');
        }
        $clients = User::where('role', 'client')->get();
        $vehicles = Vehicle::where('status', 'available')->with('agency')->get();

        if ($clients->isEmpty() || $vehicles->isEmpty()) {
            $this->command->warn('⚠ Pas assez de clients ou véhicules pour créer des réservations.');

            return;
        }

        $counter = 1;
        $year = now()->year;

        $scenarios = [
            ['days_offset_start' => -30, 'days' => 5,  'status' => 'completed'],
            ['days_offset_start' => -20, 'days' => 7,  'status' => 'completed'],
            ['days_offset_start' => -15, 'days' => 3,  'status' => 'completed'],
            ['days_offset_start' => -45, 'days' => 14, 'status' => 'completed'],
            ['days_offset_start' => -10, 'days' => 2,  'status' => 'completed'],

            ['days_offset_start' => -25, 'days' => 4,  'status' => 'cancelled'],
            ['days_offset_start' => -12, 'days' => 6,  'status' => 'cancelled'],

            ['days_offset_start' => -2,  'days' => 5,  'status' => 'confirmed'],
            ['days_offset_start' => -1,  'days' => 3,  'status' => 'confirmed'],
            ['days_offset_start' => 0,   'days' => 7,  'status' => 'confirmed'],

            ['days_offset_start' => 5,   'days' => 10, 'status' => 'confirmed'],
            ['days_offset_start' => 10,  'days' => 14, 'status' => 'confirmed'],
            ['days_offset_start' => 15,  'days' => 30, 'status' => 'confirmed'],

            ['days_offset_start' => 3,   'days' => 2,  'status' => 'pending'],
            ['days_offset_start' => 7,   'days' => 5,  'status' => 'pending'],
            ['days_offset_start' => 20,  'days' => 7,  'status' => 'pending'],
            ['days_offset_start' => 30,  'days' => 14, 'status' => 'pending'],
        ];

        foreach ($scenarios as $scenario) {
            $vehicle = $vehicles->random();
            $client = $clients->random();
            $startDate = Carbon::now()->addDays($scenario['days_offset_start']);
            $endDate = (clone $startDate)->addDays($scenario['days']);
            $price = $vehicle->getPriceForDuration($scenario['days']);
            $ref = "RES-{$year}-".str_pad($counter++, 5, '0', STR_PAD_LEFT);

            Reservation::create([
                'reference' => $ref,
                'vehicle_id' => $vehicle->id,
                'agency_id' => $vehicle->agency_id,
                'user_id' => $client->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'duration_days' => $scenario['days'],
                'price_applied' => $price,
                'total_price' => $price * $scenario['days'],
                'status' => $scenario['status'],
                'confirmed_at' => in_array($scenario['status'], ['confirmed', 'completed'])
                                    ? now() : null,
                'completed_at' => $scenario['status'] === 'completed' ? now() : null,
                'cancelled_at' => $scenario['status'] === 'cancelled' ? now() : null,
                'cancelled_by' => $scenario['status'] === 'cancelled' ? 'client' : null,
            ]);
        }

        for ($i = 0; $i < 8; $i++) {
            $vehicle = $vehicles->random();
            $client = $clients->random();
            $days = fake()->numberBetween(1, 20);
            $startDate = Carbon::now()->addDays(fake()->numberBetween(-60, 60));
            $endDate = (clone $startDate)->addDays($days);
            $price = $vehicle->getPriceForDuration($days);
            $ref = "RES-{$year}-".str_pad($counter++, 5, '0', STR_PAD_LEFT);

            Reservation::create([
                'reference' => $ref,
                'vehicle_id' => $vehicle->id,
                'agency_id' => $vehicle->agency_id,
                'user_id' => $client->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'duration_days' => $days,
                'price_applied' => $price,
                'total_price' => $price * $days,
                'status' => fake()->randomElement(['pending', 'confirmed', 'completed']),
            ]);
        }

        $this->command->info('✅ '.($counter - 1).' réservations créées avec succès.');
    }
}
