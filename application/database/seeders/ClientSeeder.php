<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Les données d’exemple sont réservées à local/testing.');
        }
        $client = User::firstOrCreate(
            ['email' => 'client@carloc.ma'],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Mansouri',
                'phone' => '0670123456',
                'role' => 'client',
                'has_store' => false,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Client@2026!'),
            ]
        );
        $client->assignRole('client');

        User::factory()
            ->count(20)
            ->client()
            ->create()
            ->each(fn ($user) => $user->assignRole('client'));

        $this->command->info('✅ 21 clients créés avec succès.');
    }
}
