<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Profile;

class ProfileUserRelateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$users = User::all();

    	$hil = Profile::findOrFail(1);

    	$hi = Profile::findOrFail(2);

    	$ice = Profile::findOrFail(3);

    	foreach($users as $user) {
    		$user->profiles()->attach($hil);
    		$user->profiles()->attach($hi);
    		if($user->hasRole('admin')) {
    			$user->profiles()->attach($ice);
    		}
    	}
    }
}
