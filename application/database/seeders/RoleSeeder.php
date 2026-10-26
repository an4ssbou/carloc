<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();


        $permissions = [
            'vehicles.view',
            'vehicles.create',
            'vehicles.edit',
            'vehicles.delete',

            'reservations.view',
            'reservations.create',
            'reservations.manage',


            'agency.manage',
            'agencies.admin',

            'users.admin',

            'dashboard.agency',
            'dashboard.admin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $clientRole->syncPermissions([
            'vehicles.view',
            'reservations.view',
            'reservations.create',
        ]);

        $agencyRole = Role::firstOrCreate(['name' => 'agency_owner']);
        $agencyRole->syncPermissions([
            'vehicles.view',
            'vehicles.create',
            'vehicles.edit',
            'vehicles.delete',
            'reservations.view',
            'reservations.create',
            'reservations.manage',
            'agency.manage',
            'dashboard.agency',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole->syncPermissions(Permission::all());

        $this->command->info('✅ Rôles et permissions créés avec succès.');
    }
}