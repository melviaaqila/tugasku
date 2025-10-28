<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Kantor permissions
            'kantor.view',
            'kantor.create',
            'kantor.edit',
            'kantor.delete',

            // Divisi permissions
            'divisi.view',
            'divisi.create',
            'divisi.edit',
            'divisi.delete',

            // Role permissions
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',

            // Permission management (opsional)
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',

            // Task permissions
            'tugasku.view',
            'tugasku.create',
            'tugasku.edit',
            'tugasku.delete',

            // Routine Task permissions
            'tugasrutin.view',
            'tugasrutin.create',
            'tugasrutin.edit',
            'tugasrutin.delete',

            // History permissions
            'history.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
