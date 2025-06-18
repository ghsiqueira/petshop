<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        Permission::create(['name' => 'manage_products']);
        Permission::create(['name' => 'manage_services']);
        Permission::create(['name' => 'manage_employees']);
        Permission::create(['name' => 'manage_petshops']);
        Permission::create(['name' => 'schedule_appointments']);
        Permission::create(['name' => 'manage_appointments']);
        Permission::create(['name' => 'view_appointments']);
        Permission::create(['name' => 'place_orders']);
        Permission::create(['name' => 'manage_orders']);
        Permission::create(['name' => 'write_reviews']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_roles']);
        Permission::create(['name' => 'manage_permissions']);

        // Criar papéis e atribuir permissões
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $petshopRole = Role::create(['name' => 'petshop']);
        $petshopRole->givePermissionTo([
            'manage_products',
            'manage_services',
            'manage_employees',
            'manage_appointments',
            'manage_orders'
        ]);

        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view_appointments',
            'manage_appointments'
        ]);

        $clientRole = Role::create(['name' => 'client']);
        $clientRole->givePermissionTo([
            'schedule_appointments',
            'place_orders',
            'write_reviews'
        ]);
    }
}