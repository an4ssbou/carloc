<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $vehicle    = Vehicle::factory()->create();
        $startDate  = fake()->dateTimeBetween('-2 months', '+1 month');
        $days       = fake()->numberBetween(1, 30);
        $endDate    = (clone $startDate)->modify("+{$days} days");
        $priceApplied = $vehicle->getPriceForDuration($days);
        $year       = now()->year;

        static $counter = 1;

        return [
            'reference'    => "RES-{$year}-" . str_pad($counter++, 5, '0', STR_PAD_LEFT),
            'vehicle_id'   => $vehicle->id,
            'agency_id'    => $vehicle->agency_id,
            'user_id'      => User::factory()->client(),
            'start_date'   => $startDate->format('Y-m-d'),
            'end_date'     => $endDate->format('Y-m-d'),
            'duration_days'=> $days,
            'price_applied'=> $priceApplied,
            'total_price'  => $priceApplied * $days,
            'status'       => fake()->randomElement([
                'pending', 'confirmed', 'confirmed',
                'completed', 'cancelled',
            ]),
            'client_notes' => fake()->optional(0.3)->sentence(),
        ];
    }


    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function confirmed(): static
    {
        return $this->state([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state([
            'status'       => 'completed',
            'confirmed_at' => now()->subDays(10),
            'completed_at' => now()->subDays(2),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status'               => 'cancelled',
            'cancellation_reason'  => fake()->sentence(),
            'cancelled_by'         => fake()->randomElement(['client', 'agency']),
            'cancelled_at'         => now(),
        ]);
    }
}