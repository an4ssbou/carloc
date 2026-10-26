<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Les données d’exemple sont réservées à local/testing.');
        }
        $admin = User::firstOrCreate(
            ['email' => 'admin@carloc.ma'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '0600000000',
                'role' => 'super_admin',
                'has_store' => false,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@2026!'),
            ]
        );

        $admin->assignRole('super_admin');

        $this->command?->info('Compte administrateur d’exemple local créé.');
    }
}
