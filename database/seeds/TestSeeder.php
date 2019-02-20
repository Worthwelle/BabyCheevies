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

        // cheevies
        $edit_cheevies = Permission::create(['name' => 'edit_achievements', 'label' => 'edit achievements']);
        
        // users
        $delete_users = Permission::create(['name' => 'delete_users', 'label' => 'delete users']);
        $edit_users   = Permission::create(['name' => 'edit_users',   'label' => 'edit users']);
        
        // roles
        $view_roles   = Permission::create(['name' => 'view_roles',   'label' => 'view roles']);
        $create_roles = Permission::create(['name' => 'create_roles', 'label' => 'create roles']);
        $edit_roles   = Permission::create(['name' => 'edit_roles',   'label' => 'edit roles']);
        $delete_roles = Permission::create(['name' => 'delete_roles', 'label' => 'delete roles']);
        
        // permissions
        $view_permissions    = Permission::create(['name' => 'view_permissions',   'label' => 'view permissions']);
        $create_permissions  = Permission::create(['name' => 'create_permissions', 'label' => 'create permissions']);
        $edit_permissions    = Permission::create(['name' => 'edit_permissions',   'label' => 'edit permissions']);
        $delete_permissions  = Permission::create(['name' => 'delete_permissions', 'label' => 'delete permissions']);
        
        // administrator permissions
        $admin = Role::create(['name' => 'administrator', 'label' => 'Administrator']);
        $admin->givePermissionTo($edit_cheevies);
        $admin->givePermissionTo($delete_users);
        $admin->givePermissionTo($edit_users);
        $admin->givePermissionTo($view_roles);
        $admin->givePermissionTo($create_roles);
        $admin->givePermissionTo($edit_roles);
        $admin->givePermissionTo($delete_roles);
        $admin->givePermissionTo($view_permissions);
        $admin->givePermissionTo($create_permissions);
        $admin->givePermissionTo($edit_permissions);
        $admin->givePermissionTo($delete_permissions);
        
        $editor = Role::create(['name' => 'editor',        'label' => 'Editor']);
        $editor->givePermissionTo($edit_cheevies);
    }
}
