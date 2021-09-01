<?php

namespace Database\Seeders;

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
        $teacherRole = Role::create(['name' => 'teacher']);
        $user = Role::create(['name' => 'user']);

        Permission::create(['name' => 'invite user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);

        $adminRole->givePermissionTo('invite user');
        $super_admin_role->givePermissionTo('edit user');
        //$super_admin_role->givePermissionTo('add user');
        $super_admin_role->givePermissionTo('delete user');
        
    }
}
