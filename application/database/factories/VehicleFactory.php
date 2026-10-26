<?php

namespace Database\Factories;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{
    private array $brands = [
        'Dacia'      => ['Sandero', 'Logan', 'Duster', 'Spring'],
        'Renault'    => ['Clio', 'Megane', 'Captur', 'Kangoo'],
        'Peugeot'    => ['208', '308', '3008', '5008'],
        'Volkswagen' => ['Polo', 'Golf', 'Tiguan', 'T-Roc'],
        'Toyota'     => ['Yaris', 'Corolla', 'Hilux', 'RAV4'],
        'Hyundai'    => ['i10', 'i20', 'Tucson', 'Kona'],
        'Kia'        => ['Picanto', 'Rio', 'Sportage', 'Stonic'],
        'Mercedes'   => ['Classe A', 'Classe C', 'GLC', 'Sprinter'],
    ];

    private array $colors = [
        'Blanc', 'Noir', 'Gris', 'Rouge',
        'Bleu', 'Argent', 'Vert', 'Beige',
    ];

    public function definition(): array
    {
        $brand  = fake()->randomElement(array_keys($this->brands));
        $model  = fake()->randomElement($this->brands[$brand]);
        $pricePerDay = fake()->numberBetween(200, 600);

        return [
            'uuid'                    => Str::uuid(),
            'agency_id'               => Agency::factory(),
            'brand'                   => $brand,
            'model'                   => $model,
            'year'                    => fake()->numberBetween(2018, 2024),
            'registration_number'     => strtoupper(fake()->unique()->bothify('??-####-??')),
            'mileage'                 => fake()->numberBetween(0, 120000),
            'color'                   => fake()->randomElement($this->colors),
            'condition'               => fake()->randomElement(['new', 'good', 'good', 'used']),
            'seats'                   => fake()->randomElement([2, 4, 5, 5, 5, 7]),
            'fuel_type'               => fake()->randomElement(['gasoline', 'diesel', 'diesel', 'electric', 'hybrid']),
            'transmission'            => fake()->randomElement(['manual', 'manual', 'automatic']),
            'description'             => fake()->paragraph(),
            'has_gps'                 => fake()->boolean(40),
            'gps_latitude'            => null,
            'gps_longitude'           => null,

            'price_per_day'           => $pricePerDay,
            'price_week'              => round($pricePerDay * 0.85),
            'price_two_weeks'         => round($pricePerDay * 0.75),
            'price_month'             => round($pricePerDay * 0.65),
            'status'                  => 'available',
            'is_manually_unavailable' => false,
            'is_featured'             => false,
        ];
    }


    public function withGps(): static
    {
        $locations = [
            ['lat' => 33.5731, 'lng' => -7.5898],
            ['lat' => 34.0209, 'lng' => -6.8416],
            ['lat' => 31.6295, 'lng' => -7.9811],
            ['lat' => 34.0331, 'lng' => -5.0003],
            ['lat' => 35.7595, 'lng' => -5.8340],
            ['lat' => 30.4278, 'lng' => -9.5981],
        ];

        $location = fake()->randomElement($locations);

        return $this->state([
            'has_gps'       => true,
            'gps_latitude'  => $location['lat'] + fake()->randomFloat(4, -0.05, 0.05),
            'gps_longitude' => $location['lng'] + fake()->randomFloat(4, -0.05, 0.05),
        ]);
    }

    public function unavailable(): static
    {
        return $this->state([
            'status'                  => 'unavailable',
            'is_manually_unavailable' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function luxury(): static
    {
        return $this->state([
            'brand'        => 'Mercedes',
            'model'        => fake()->randomElement(['Classe C', 'GLC', 'Classe E']),
            'condition'    => 'new',
            'price_per_day'=> fake()->numberBetween(800, 1500),
            'has_gps'      => true,
        ]);
    }
}