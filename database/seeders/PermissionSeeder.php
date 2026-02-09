<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'create task',
            'update task',
            'assign task',
            'view all tasks',
            'view one task',
            'change task status',
            'delete task',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }

        $manager = Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'sanctum',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'sanctum',
        ]);

        // Assign permissions to manager
        $manager->syncPermissions([
            'create task',
            'update task',
            'assign task',
            'view all tasks',
            'view one task',
            'delete task',
            'change task status',
        ]);

        // Assign permissions to user
        $user->syncPermissions([
            'change task status',
        ]);
    }
}
