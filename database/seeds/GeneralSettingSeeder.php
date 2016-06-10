<?php

use Illuminate\Database\Seeder;
use App\GeneralSetting;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GeneralSetting::create([
            'DTDCUST_EMAIL_CONTENT' => null,
        ]);
    }
}
