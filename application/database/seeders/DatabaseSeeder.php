<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Les données d’exemple sont réservées à local/testing.');
        }
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            AgencySeeder::class,
            ClientSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
