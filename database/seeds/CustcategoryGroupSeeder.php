<?php

use App\Custcategory;
use App\CustcategoryGroup;
use Illuminate\Database\Seeder;

class CustcategoryGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $a1 = CustcategoryGroup::create([
            'name' => 'A'
        ]);

        $b1 = Custcategory::whereIn('id', [2, 14])->get();

        if($b1) {
            foreach($b1 as $custcategory) {
                $custcategory->custcategory_group_id = $a1->id;
                $custcategory->save();
            }
        }


        $a2 = CustcategoryGroup::create([
            'name' => 'E'
        ]);

        $b2 = Custcategory::whereIn('id', [29, 43])->get();

        if($b2) {
            foreach($b2 as $custcategory) {
                $custcategory->custcategory_group_id = $a2->id;
                $custcategory->save();
            }
        }

        $a3 = CustcategoryGroup::create([
            'name' => 'EVENT'
        ]);

        $b3 = Custcategory::whereIn('id', [5, 96])->get();

        if($b3) {
            foreach($b3 as $custcategory) {
                $custcategory->custcategory_group_id = $a3->id;
                $custcategory->save();
            }
        }

        $a4 = CustcategoryGroup::create([
            'name' => 'EXPORT'
        ]);

        $b4 = Custcategory::whereIn('id', [91])->get();

        if($b4) {
            foreach($b4 as $custcategory) {
                $custcategory->custcategory_group_id = $a4->id;
                $custcategory->save();
            }
        }

        $a5 = CustcategoryGroup::create([
            'name' => 'GT'
        ]);

        $b5 = Custcategory::whereIn('id', [3,4,97,94,101,84,107,106,105,103,104,93,95,37])->get();

        if($b5) {
            foreach($b5 as $custcategory) {
                $custcategory->custcategory_group_id = $a5->id;
                $custcategory->save();
            }
        }

        $a6 = CustcategoryGroup::create([
            'name' => 'MT'
        ]);

        $b6 = Custcategory::whereIn('id', [6,81,7,72,100,65,87,88,92,63,82,56,58,73,57,86,90])->get();

        if($b6) {
            foreach($b6 as $custcategory) {
                $custcategory->custcategory_group_id = $a6->id;
                $custcategory->save();
            }
        }

        $a7 = CustcategoryGroup::create([
            'name' => 'ONLINE'
        ]);

        $b7 = Custcategory::whereIn('id', [8,64,98,55,89,59,9,10])->get();

        if($b7) {
            foreach($b7 as $custcategory) {
                $custcategory->custcategory_group_id = $a7->id;
                $custcategory->save();
            }
        }

        $a8 = CustcategoryGroup::create([
            'name' => 'SCHOOL'
        ]);

        $b8 = Custcategory::whereIn('id', [26,27,25,11,24])->get();

        if($b8) {
            foreach($b8 as $custcategory) {
                $custcategory->custcategory_group_id = $a8->id;
                $custcategory->save();
            }
        }

        $a9 = CustcategoryGroup::create([
            'name' => 'SUB-DIST'
        ]);

        $b9 = Custcategory::whereIn('id', [102])->get();

        if($b9) {
            foreach($b9 as $custcategory) {
                $custcategory->custcategory_group_id = $a9->id;
                $custcategory->save();
            }
        }

        $a10 = CustcategoryGroup::create([
            'name' => 'VM'
        ]);

        $b10 = Custcategory::whereIn('id', [41,23,54,61,60,69,66,67,74,83,109,31,32,33,48,80,34,35,36,39,70,42,16,17,18,30,62,20,21,22])->get();

        if($b10) {
            foreach($b10 as $custcategory) {
                $custcategory->custcategory_group_id = $a10->id;
                $custcategory->save();
            }
        }

        $a11 = CustcategoryGroup::create([
            'name' => 'VM-EVENT'
        ]);

        $b11 = Custcategory::whereIn('id', [51,19])->get();

        if($b11) {
            foreach($b11 as $custcategory) {
                $custcategory->custcategory_group_id = $a11->id;
                $custcategory->save();
            }
        }

        $a12 = CustcategoryGroup::create([
            'name' => 'VM-FRANCHISE'
        ]);

        $b12 = Custcategory::whereIn('id', [15])->get();

        if($b12) {
            foreach($b12 as $custcategory) {
                $custcategory->custcategory_group_id = $a12->id;
                $custcategory->save();
            }
        }

        $a13 = CustcategoryGroup::create([
            'name' => 'VM-LEASING'
        ]);


        $b13 = Custcategory::whereIn('id', [38,46,44,45,47,49,50,52,68,71,85,108,75,99,53])->get();

        if($b13) {
            foreach($b13 as $custcategory) {
                $custcategory->custcategory_group_id = $a13->id;
                $custcategory->save();
            }
        }

        $a14 = CustcategoryGroup::create([
            'name' => 'VM-SELLING'
        ]);

        $b14 = Custcategory::whereIn('id', [28])->get();

        if($b14) {
            foreach($b14 as $custcategory) {
                $custcategory->custcategory_group_id = $a14->id;
                $custcategory->save();
            }
        }
    }
}
