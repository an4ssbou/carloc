<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Les comptes de démonstration sont réservés aux environnements local/testing.');
        }
        $this->call(RoleSeeder::class);
        foreach (['cin-demo.txt', 'license-demo.txt'] as $file) {
            Storage::disk('local')->put('synthetic/'.$file, 'DEMONSTRATION : document synthétique, aucune identité réelle ni vérification.');
        }
        $users = [];
        foreach ([['demo@carloc.test', 'Client', 'Demo', 'client', 'DEMOCLIENT'], ['agence@carloc.test', 'Agence', 'Demo', 'agency_owner', 'DEMOAGENCE']] as [$email, $first, $last, $role, $cin]) {
            $user = User::firstOrNew(['email' => $email]);
            if (! $user->exists) {
                $user->forceFill([
                    'first_name' => $first, 'last_name' => $last, 'role' => $role,
                    'cin_number' => $cin, 'cin_photo' => 'synthetic/cin-demo.txt', 'license_photo' => 'synthetic/license-demo.txt',
                    'email_verified_at' => now(), 'is_active' => true, 'has_store' => $role === 'agency_owner',
                    'password' => Hash::make('Demo-Local-2026!'),
                ])->save();
                $user->syncRoles([$role]);
            }
            $users[$role] = $user;
        }
        $agency = Agency::firstOrNew(['email' => 'agence@carloc.test']);
        if (! $agency->exists) {
            $agency->fill([
                'name' => 'Agence Démonstration', 'slug' => 'agence-demonstration', 'city' => 'Casablanca', 'address' => 'Adresse synthétique de démonstration',
                'email' => 'agence@carloc.test', 'founder_id' => $users['agency_owner']->id,
                'description' => 'Jeu de démonstration local sans client réel.', 'status' => 'active',
            ]);
            // Re-running a seed must not send an unchanged demo agency back to pending.
            $agency->saveQuietly();
            if (! $agency->slug) {
                $agency->updateQuietly(['slug' => 'agence-demonstration']);
            }
        }
        $vehicle = Vehicle::firstOrNew(['registration_number' => 'DEMO-001']);
        if (! $vehicle->exists) {
            $vehicle->fill([
                'agency_id' => $agency->id, 'brand' => 'Dacia', 'model' => 'Sandero', 'year' => 2024,
                'mileage' => 10000, 'color' => 'Rouge', 'condition' => 'good', 'seats' => 5,
                'fuel_type' => 'gasoline', 'transmission' => 'manual', 'has_gps' => false,
                'description' => 'Véhicule synthétique pour tester la réservation locale.',
                'price_per_day' => 300, 'price_week' => 1800, 'price_two_weeks' => 3300, 'price_month' => 6000,
                'status' => 'available', 'is_manually_unavailable' => false, 'is_featured' => true,
            ])->save();
        }
        $this->command?->info('Données de démonstration synthétiques prêtes. Aucun paiement réel.');
    }
}
