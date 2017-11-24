<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleFranchiseeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'franchisee',
            'label' => 'Franchisee/Leasse',
            'remark' => '',
        ]);
    }
}
