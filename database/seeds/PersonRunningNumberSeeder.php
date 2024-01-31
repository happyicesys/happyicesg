<?php

use App\Person;
use App\Traits\HasRunningNumber;
use Illuminate\Database\Seeder;

class PersonRunningNumberSeeder extends Seeder
{
    use HasRunningNumber;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $people = Person::where('cust_id', 'NOT REGEXP', '^[HD]')->orderBy('updated_at')->get();
        foreach ($people as $person) {
            $person->code = $this->generateRunningNumber($person);
            $person->save();
        }
    }
}
