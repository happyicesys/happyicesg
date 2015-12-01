<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //user
        Permission::create([
            'name' => 'create_user',
            'label' => 'Create User',
            'remark' => '',
        ]);

        Permission::create([
            'name' => 'view_user',
            'label' => 'View User',
            'remark' => '',
        ]);        

        Permission::create([
            'name' => 'edit_user',
            'label' => 'Edit User',
            'remark' => '',
        ]);

        Permission::create([
            'name' => 'delete_user',
            'label' => 'Delete User',
            'remark' => '',
        ]);

        //role
        Permission::create([
            'name' => 'create_role',
            'label' => 'Create Role',
            'remark' => '',
        ]);

        Permission::create([
            'name' => 'view_role',
            'label' => 'View Role',
            'remark' => '',
        ]);        

        Permission::create([
            'name' => 'edit_role',
            'label' => 'Edit Role',
            'remark' => '',
        ]);

        Permission::create([
            'name' => 'delete_role',
            'label' => 'Delete Role',
            'remark' => '',
        ]); 

        Permission::create([
            'name' => 'view_permission',
            'label' => 'View Permission',
            'remark' => '',
        ]);                                                       
    } 
}
