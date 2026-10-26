<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AgencyFactory extends Factory
{
    private array $agencyNames = [
        'Auto Prestige Maroc',
        'Location Express',
        'Drive Easy',
        'Maghreb Car Rental',
        'Atlas Auto',
        'Premium Cars Maroc',
        'Fast Drive',
        'Sun Car Rental',
        'Royal Auto Location',
        'Casablanca Cars',
    ];

    private array $cities = [
        'Casablanca', 'Rabat', 'Marrakech',
        'Fès', 'Tanger', 'Agadir',
        'Oujda', 'Meknès', 'Tétouan',
    ];

    public function definition(): array
    {
        $name = fake()->unique()->randomElement($this->agencyNames);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'city'        => fake()->randomElement($this->cities),
            'address'     => fake()->address(),
            'phone'       => fake()->numerify('05########'),
            'email'       => fake()->companyEmail(),
            'founder_id'  => User::factory()->agencyOwner(),
            'description' => fake()->paragraphs(2, true),
            'logo'        => null,
            'status'      => 'active',
        ];
    }


    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'           => 'rejected',
            'rejection_reason' => 'Documents incomplets ou informations incorrectes.',
            'rejected_at'      => now(),
        ]);
    }
}