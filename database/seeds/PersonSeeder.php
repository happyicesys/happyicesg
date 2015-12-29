<?php

use Illuminate\Database\Seeder;
use App\Person;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Person::create([
            'cust_id' => 'S001L',
            'company' => 'Drink Stall @ Fuhua Sec School',
            'bill_address' => 'Drink Stall @ Fuhua Sec School',
            'del_address' => '5 Jurong West Street 41',
            'del_postcode' => '649410',
            'name' => 'Mr Ang',
            'contact' => '94870491',
            'alt_contact' => '84287363',
            'payterm' => 'C.O.D',
            'cost_rate' => 75,
        ]); 

        Person::create([
            'cust_id' => 'S006L',
            'company' => 'Drink Stall @ Jurong Sec School',
            'bill_address' => 'Drink Stall @ Jurong Sec School',
            'del_address' => '31 Yuan Ching Road',
            'del_postcode' => '618652',
            'name' => 'Mr Ahmad',
            'contact' => '96363680',
            'payterm' => 'C.O.D',
            'cost_rate' => 75,
        ]); 

        Person::create([
            'cust_id' => 'C004L',
            'company' => 'Hong Kong Restaurant',
            'bill_address' => '#01-05, 1 Yuan Ching Road',
            'del_address' => '#01-05, 1 Yuan Ching Road',
            'del_postcode' => '618640',
            'name' => 'Raymond',
            'contact' => '98553706',
            'payterm' => 'C.O.D',
            'cost_rate' => 70,
        ]);

        Person::create([
            'cust_id' => 'C009L',
            'company' => 'Happy',
            'bill_address' => '#02-10A Greenridge Shopping Center, Block 524A, Jelapang Road',
            'del_address' => '#02-10A Greenridge Shopping Center, Block 524A, Jelapang Road',
            'del_postcode' => '671524',
            'name' => 'Nicholas Choy',
            'contact' => '93877794',
            'payterm' => 'C.O.D',
            'cost_rate' => 75,
        ]);

        Person::create([
            'cust_id' => 'E1406L',
            'company' => 'Sentosa TGIF',
            'bill_address' => 'TGIF',
            'del_address' => 'Sentosa Broadwalk, Sentosa',
            'del_postcode' => '098585',
            'name' => 'Mr Seet',
            'contact' => '92381625',
            'payterm' => 'C.O.D',
            'cost_rate' => 60,
        ]); 

        Person::create([
            'cust_id' => 'E15084L',
            'company' => 'Candy @ Expo',
            'bill_address' => 'Candy',
            'del_address' => 'SFMA Expo, Hall 4 C119, 1 Expo Drive',
            'del_postcode' => '486150',
            'name' => 'Candy',
            'contact' => '90016264',
            'payterm' => 'C.O.D',
            'remark' => 'Cash Rebate during each event closure: S$30 off on every S$500',
            'cost_rate' => 60,
        ]);                                          
    }
}
