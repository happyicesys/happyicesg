<?php

use Illuminate\Database\Seeder;
use App\Role;

class SubFranchiseeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'subfranchisee',
            'label' => 'Sub-Franchisee/Leasse'
        ]);
    }
}
