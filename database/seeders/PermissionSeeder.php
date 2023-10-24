<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $super_admin_role = Role::create(['name' => 'Super-admin']);
        $teacherRole = Role::create(['name' => 'Enseignant']);
        $user = Role::create(['name' => 'Utilisateur']);

        Permission::create(['name' => 'invite user']);
        Permission::create(['name' => 'block user']);
        Permission::create(['name' => 'show user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'add user']);

        $adminRole->givePermissionTo('invite user');
        $adminRole->givePermissionTo('show user');

        $super_admin_role->givePermissionTo('edit user');
        $super_admin_role->givePermissionTo('add user');
        $super_admin_role->givePermissionTo('delete user');
    }
}
