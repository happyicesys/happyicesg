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

        Role::create([
            'name' => 'technician',
            'label' => 'Technician',
            'remark' => '',
        ]);
    }
}
