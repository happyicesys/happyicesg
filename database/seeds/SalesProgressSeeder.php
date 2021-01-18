<?php

use Illuminate\Database\Seeder;
use App\PotentialCustomer;
use App\SalesProgress;

class SalesProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $model_1 = SalesProgress::create([
            'name' => 'Sample given',
            'order' => 2
        ]);
        $model_2 = SalesProgress::create([
            'name' => 'Meet Boss',
            'order' => 3
        ]);
        $model_3 = SalesProgress::create([
            'name' => 'First try boss reject',
            'order' => 4
        ]);
        $model_4 = SalesProgress::create([
            'name' => 'Approved',
            'order' => 5
        ]);
        $model_5 = SalesProgress::create([
            'name' => '2nd try',
            'order' => 7
        ]);
        $model_6 = SalesProgress::create([
            'name' => '3rd try',
            'order' => 8
        ]);
        $model_7 = SalesProgress::create([
            'name' => 'Need follow-up',
            'order' => 1
        ]);
        $model_8 = SalesProgress::create([
            'name' => 'In-principle approved',
            'order' => 6
        ]);
        $model_9 = SalesProgress::create([
            'name' => 'Pls follow up- no stock also not allow to load',
            'order' => 9
        ]);
        $model_10 = SalesProgress::create([
            'name' => 'Pls follow up- on unpaid invoices',
            'order' => 10
        ]);
        $model_11 = SalesProgress::create([
            'name' => 'Pls follow up- bad sales, please site visit',
            'order' => 11
        ]);

        $potentialCustomers = PotentialCustomer::all();
        foreach($potentialCustomers as $potentialCustomer) {
            if($potentialCustomer->is_first) {
                $potentialCustomer->salesProgresses()->attach($model_1);
            }
            if($potentialCustomer->is_second) {
                $potentialCustomer->salesProgresses()->attach($model_2);
            }
            if($potentialCustomer->is_third) {
                $potentialCustomer->salesProgresses()->attach($model_3);
            }
            if($potentialCustomer->is_fourth) {
                $potentialCustomer->salesProgresses()->attach($model_4);
            }
            if($potentialCustomer->is_fifth) {
                $potentialCustomer->salesProgresses()->attach($model_5);
            }
            if($potentialCustomer->is_sixth) {
                $potentialCustomer->salesProgresses()->attach($model_6);
            }
            if($potentialCustomer->is_seventh) {
                $potentialCustomer->salesProgresses()->attach($model_7);
            }
            if($potentialCustomer->is_eighth) {
                $potentialCustomer->salesProgresses()->attach($model_8);
            }
        }
    }
}
