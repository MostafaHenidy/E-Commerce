<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminPermissions = [
            'manage_users',
            'manage_products',
            'manage_categories',
            'manage_vendors',
            'manage_orders',
        ];

        $vendorPermissions = [
            'manage_products',
            'manage_orders',
        ];

        $userPermissions = [
            'manage_products',
            'manage_orders',
            'manage_reviews',
            'manage_cart',
        ];

        // Create permissions
        foreach ($adminPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }

        foreach ($vendorPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'vendor']);
        }

        foreach ($userPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        $vendorRole = Role::create(['name' => 'vendor', 'guard_name' => 'vendor']);
        $userRole = Role::create(['name' => 'user', 'guard_name' => 'web']);

        $adminRole->givePermissionTo($adminPermissions);
        $vendorRole->givePermissionTo($vendorPermissions);
        $userRole->givePermissionTo($userPermissions);
    }
}
