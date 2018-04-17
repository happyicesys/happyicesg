<?php

use Illuminate\Database\Seeder;
use App\Role;

class SupervisorMsiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'supervisor_msia',
            'label' => 'Malaysia Supervisor'
        ]);
    }
}
