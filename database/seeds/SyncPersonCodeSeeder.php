<?php

use App\CustPrefix;
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
            $extractedPrefix = trim(preg_replace('/[^a-zA-Z]/', '', $person->cust_id));
            $custPrefix = CustPrefix::updateOrCreate(['code' => $extractedPrefix]);
            $person->cust_prefix_id = $custPrefix->id;
            $person->save();
        }
    }
}
