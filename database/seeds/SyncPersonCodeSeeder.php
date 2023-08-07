<?php

use App\Person;
use Illuminate\Database\Seeder;

class SyncPersonCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $people = Person::all();

        foreach($people as $person) {
            $person->code = preg_replace('/[^0-9]/', '', $person->cust_id);
            $person->save();
        }
    }
}
