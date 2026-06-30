<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            '*',

            'dashboard.view',

            'risk.view',
            'risk.create',
            'risk.edit',
            'risk.delete',
            'risk.approve',
            'risk.export',

            'risk-category.view',
            'risk-category.create',
            'risk-category.edit',
            'risk-category.delete',

            'risk-matrix.view',
            'risk-matrix.manage',

            'report.view',
            'report.export',

            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'role.assign-permission',

            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',

            'setting.view',
            'setting.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superadmin = Role::findOrCreate('superadmin', 'web');
        $admin = Role::findOrCreate('admin', 'web');
        $user = Role::findOrCreate('user', 'web');

        $superadmin->syncPermissions(['*']);

        $admin->syncPermissions([
            'dashboard.view',

            'risk.view',
            'risk.create',
            'risk.edit',
            'risk.approve',
            'risk.export',

            'risk-category.view',
            'risk-matrix.view',

            'report.view',
            'report.export',

            'user.view',
            'user.create',
            'user.edit',

            'role.view',
            'permission.view',
        ]);

        $user->syncPermissions([
            'dashboard.view',
            'risk.view',
            'risk.create',
        ]);

        $defaultSuperadmin = User::firstOrCreate(
            ['email' => 'superadmin@risk-dashboard.test'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make('password'),
            ]
        );

        $defaultSuperadmin->syncRoles(['superadmin']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
