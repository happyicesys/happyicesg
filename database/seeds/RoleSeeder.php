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
/*         Role::create([
            'name' => 'admin',
            'label' => 'Administrator',
            'remark' => '',
        ]);

        Role::create([
            'name' => 'user',
            'label' => 'User',
            'remark' => '',
        ]); */
/*
        Role::create([
            'name' => 'technician',
            'label' => 'Technician',
            'remark' => '',
        ]); */
/*
        Role::create([
            'name' => 'salesperson',
            'label' => 'Sales Person',
            'remark' => '',
        ]); */

        Role::create([
            'name' => 'driver-supervisor',
            'label' => 'Driver Supervisor',
            'remark' => '',
        ]);
    }
}
