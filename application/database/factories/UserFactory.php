<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'        => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'phone'             => fake()->numerify('06########'),
            'cin_number'        => fake()->unique()->regexify('[A-Z]{1,2}[0-9]{6}'),
            'profile_photo'     => null,
            'cin_photo'         => null,
            'license_photo'     => null,
            'role'              => 'client',
            'has_store'         => false,
            'is_active'         => true,
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
        ];
    }


    public function superAdmin(): static
    {
        return $this->state([
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'admin@carloc.ma',
            'role'       => 'super_admin',
            'password'   => Hash::make('Admin@2026!'),
        ]);
    }

    public function agencyOwner(): static
    {
        return $this->state([
            'role'      => 'agency_owner',
            'has_store' => true,
        ]);
    }

    public function client(): static
    {
        return $this->state([
            'role' => 'client',
        ]);
    }

    public function unverified(): static
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }
}