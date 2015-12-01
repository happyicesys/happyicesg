<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'admin',
            'label' => 'Administrator',
            'remark' => '',
        ]); 

        Role::create([
            'name' => 'rgm',
            'label' => 'Regional Manager',
            'remark' => '',
        ]);

        Role::create([
            'name' => 'manager',
            'label' => 'Manager',
            'remark' => '',
        ]);

        Role::create([
            'name' => 'user',
            'label' => 'User',
            'remark' => '',
        ]);                 
    }
}
