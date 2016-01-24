<?php

use Illuminate\Database\Seeder;
use App\Profile;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profile::create([
            'name' => 'HAPPY ICE LOGISTIC PTE LTD',
            'roc_no' => '201427642H',
            'address' => '#04-125, TradeHub 21, 18 Boon Lay Way, Singapore 609966',
            'contact' => '+65 6795 0881',
            'gst' => 0,
        ]); 

        Profile::create([
            'name' => 'HAPPY ICE PTE LTD',
            'roc_no' => '201302530W',
            'address' => '#04-125, TradeHub 21, 18 Boon Lay Way, Singapore 609966',
            'contact' => '+65 6795 0881',
            'gst' => 1,
        ]);         
    }
}
