<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'kepala_sekolah', 'guru'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-master-data',
            'manage-categories',
            'manage-point-rules',
            'input-violation',
            'input-achievement',
            'manage-improvement',
            'view-reports',
            'manage-settings',
            'view-activity-log',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $map = [
            'super_admin' => $permissions,
            'admin' => [
                'view-dashboard',
                'manage-master-data',
                'manage-categories',
                'manage-point-rules',
                'input-violation',
                'input-achievement',
                'manage-improvement',
                'view-reports',
                'view-activity-log',
            ],
            'kepala_sekolah' => [
                'view-dashboard',
                'view-reports',
            ],
            'guru' => [
                'view-dashboard',
                'input-violation',
                'input-achievement',
                'manage-improvement',
            ],
        ];

        foreach ($map as $roleName => $permissionNames) {
            $role = Role::findByName($roleName, 'web');
            $role->syncPermissions($permissionNames);
        }
    }
}
