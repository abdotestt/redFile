<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

        // Create permissions
        $permissions = [
            'create document',
            'edit document',
            'delete document',
            'view document',
            'create category',
            'edit category',
            'delete category',
            'view category',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all()); // Admin gets all permissions
        $user->givePermissionTo(['create document', 'edit document','view document','delete document','view category']); // Editor can create and edit
    }
}
