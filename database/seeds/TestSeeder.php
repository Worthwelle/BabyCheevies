<?php

use Illuminate\Database\Seeder;

use BabyCheevies\Permission;
use BabyCheevies\Role;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin  = Role::create(['name' => 'administrator', 'label' => 'Administrator']);
        $editor = Role::create(['name' => 'editor',        'label' => 'Editor']);

        $edit_cheevies = Permission::create(['name' => 'edit_achievements', 'label' => 'edit achievements']);
        $delete_users  = Permission::create(['name' => 'delete_users',      'label' => 'delete users']);
        $edit_users  = Permission::create(['name' => 'edit_users',      'label' => 'edit users']);
        $view_roles  = Permission::create(['name' => 'view_roles',      'label' => 'view roles']);
        $create_roles  = Permission::create(['name' => 'create_roles',      'label' => 'create roles']);
        $edit_roles  = Permission::create(['name' => 'edit_roles',      'label' => 'edit roles']);
        $delete_roles  = Permission::create(['name' => 'delete_roles',      'label' => 'delete roles']);
        
        $editor->givePermissionTo($edit_cheevies);
        $admin ->givePermissionTo($edit_cheevies);
        $admin ->givePermissionTo($delete_users);
        $admin ->givePermissionTo($edit_users);
        $admin ->givePermissionTo($view_roles);
        $admin ->givePermissionTo($create_roles);
        $admin ->givePermissionTo($edit_roles);
        $admin ->givePermissionTo($delete_roles);
    }
}
