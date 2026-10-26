<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Les données d’exemple sont réservées à local/testing.');
        }
        $owner1 = User::firstOrCreate(
            ['email' => 'agency1@carloc.ma'],
            [
                'first_name' => 'Karim',
                'last_name' => 'Benali',
                'phone' => '0661234567',
                'role' => 'agency_owner',
                'has_store' => true,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Agency@2026!'),
            ]
        );
        $owner1->assignRole('agency_owner');

        $agency1 = Agency::firstOrCreate(
            ['slug' => 'auto-prestige-casablanca'],
            [
                'name' => 'Auto Prestige Casablanca',
                'city' => 'Casablanca',
                'address' => '45 Boulevard Mohammed V, Casablanca',
                'phone' => '0522123456',
                'email' => 'contact@autoprestige.ma',
                'founder_id' => $owner1->id,
                'description' => 'Votre partenaire de confiance pour la location de véhicules à Casablanca. Flotte moderne et entretenue régulièrement.',
                'status' => 'active',
                'approved_at' => now(),
            ]
        );

        Vehicle::factory()->count(3)->for($agency1)->create();
        Vehicle::factory()->count(2)->withGps()->for($agency1)->create();
        Vehicle::factory()->featured()->withGps()->for($agency1)->create();

        $owner2 = User::firstOrCreate(
            ['email' => 'agency2@carloc.ma'],
            [
                'first_name' => 'Fatima',
                'last_name' => 'El Idrissi',
                'phone' => '0662345678',
                'role' => 'agency_owner',
                'has_store' => true,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Agency@2026!'),
            ]
        );
        $owner2->assignRole('agency_owner');

        $agency2 = Agency::firstOrCreate(
            ['slug' => 'atlas-auto-marrakech'],
            [
                'name' => 'Atlas Auto Marrakech',
                'city' => 'Marrakech',
                'address' => '12 Rue de la Liberté, Marrakech',
                'phone' => '0524987654',
                'email' => 'info@atlasauto.ma',
                'founder_id' => $owner2->id,
                'description' => 'Découvrez Marrakech et ses environs avec notre flotte de véhicules confortables. Location courte et longue durée disponible.',
                'status' => 'active',
                'approved_at' => now(),
            ]
        );

        Vehicle::factory()->count(4)->for($agency2)->create();
        Vehicle::factory()->luxury()->withGps()->for($agency2)->create();
        Vehicle::factory()->featured()->for($agency2)->create();

        $owner3 = User::firstOrCreate(
            ['email' => 'agency3@carloc.ma'],
            [
                'first_name' => 'Youssef',
                'last_name' => 'Ouahbi',
                'phone' => '0663456789',
                'role' => 'agency_owner',
                'has_store' => true,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Agency@2026!'),
            ]
        );
        $owner3->assignRole('agency_owner');

        $agency3 = Agency::firstOrCreate(
            ['slug' => 'sun-car-agadir'],
            [
                'name' => 'Sun Car Agadir',
                'city' => 'Agadir',
                'address' => '7 Avenue Hassan II, Agadir',
                'phone' => '0528112233',
                'email' => 'suncar@agadir.ma',
                'founder_id' => $owner3->id,
                'description' => 'Spécialiste de la location de voitures à Agadir depuis 2015. Idéal pour explorer le Souss et le Sud marocain.',
                'status' => 'active',
                'approved_at' => now(),
            ]
        );

        Vehicle::factory()->count(5)->for($agency3)->create();
        Vehicle::factory()->withGps()->featured()->for($agency3)->create();

        $owner4 = User::firstOrCreate(
            ['email' => 'pending@carloc.ma'],
            [
                'first_name' => 'Mehdi',
                'last_name' => 'Tazi',
                'phone' => '0664567890',
                'role' => 'agency_owner',
                'has_store' => true,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Agency@2026!'),
            ]
        );
        $owner4->assignRole('agency_owner');

        Agency::firstOrCreate(
            ['slug' => 'drive-easy-rabat'],
            [
                'name' => 'Drive Easy Rabat',
                'city' => 'Rabat',
                'founder_id' => $owner4->id,
                'status' => 'pending',
                'description' => 'Agence en cours de validation.',
            ]
        );

        Agency::factory()->count(3)->active()->create()->each(function ($agency) {
            Vehicle::factory()->count(fake()->numberBetween(3, 8))->for($agency)->create();
        });

        $this->command->info('✅ Agences et véhicules créés avec succès.');
    }
}
