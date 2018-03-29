<?php

use Illuminate\Database\Seeder;
use App\Role;

class UserRoleAccountAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'accountadmin',
            'label' => 'Account/Admin'
        ]);
    }
}
