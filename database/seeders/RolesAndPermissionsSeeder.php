<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-books',
            'manage-borrows',
            'approve-ratings',
            'rate-books',
            'borrow-books',
            'manage-own-ratings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $librarianRole = Role::create(['name' => 'librarian']);
        $librarianRole->givePermissionTo(['manage-books', 'manage-borrows', 'approve-ratings']);

        $studentRole = Role::create(['name' => 'student']);
        $studentRole->givePermissionTo(['rate-books', 'borrow-books', 'manage-own-ratings']);

        $facultyRole = Role::create(['name' => 'faculty']);
        $facultyRole->givePermissionTo(['rate-books', 'borrow-books', 'manage-own-ratings']);
    }
}
