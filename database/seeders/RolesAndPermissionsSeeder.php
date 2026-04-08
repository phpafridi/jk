<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view markets', 'manage markets',
            'view shops', 'manage shops',
            'view rent', 'manage rent',
            'view sell purchase', 'manage sell purchase',
            'view construction', 'manage construction',
            'view owners', 'manage owners',
            'view customers', 'manage customers',
            'view ledger', 'manage ledger',
            'view reports', 'manage users',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view markets', 'view shops', 'view rent',
            'view sell purchase', 'view construction',
            'view owners', 'view customers', 'view ledger', 'view reports',
        ]);

        // Create default admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->assignRole('admin');

        // Create default viewer
        $viewerUser = User::firstOrCreate(
            ['email' => 'viewer@viewer.com'],
            [
                'name' => 'Viewer',
                'password' => Hash::make('password'),
            ]
        );
        $viewerUser->assignRole('viewer');
    }
}
