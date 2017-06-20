<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
/*
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(ProfileSeeder::class);
        $this->call(PersonSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(PaytermSeeder::class);
        $this->call(Item2Seeder::class);
        $this->call(PriceSeeder::class);
        $this->call(FreezerSeeder::class);
        $this->call(AccessorySeeder::class);
        $this->call(GeneralSettingSeeder::class);
        $this->call(D2dOnlineSaleSeeder::class);
        $this->call(MonthSeeder::class);
        $this->call(CustcategorySeeder::class);
        */
        $this->call(ItemcategorySeeder::class);
        Model::reguard();
    }
}
