<?php

use App\LocationType;
use Illuminate\Database\Seeder;

class LocationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LocationType::create([
            'sequence' => 1,
            'name' => 'Construction Sites',
        ]);

        LocationType::create([
            'sequence' => 2,
            'name' => 'CC & RC',
        ]);

        LocationType::create([
            'sequence' => 3,
            'name' => 'Childcare or Tuition Center',
        ]);

        LocationType::create([
            'sequence' => 4,
            'name' => 'Condo',
        ]);

        LocationType::create([
            'sequence' => 5,
            'name' => 'Corporate Office',
        ]);

        LocationType::create([
            'sequence' => 6,
            'name' => 'Factory/ Warehouse',
        ]);

        LocationType::create([
            'sequence' => 7,
            'name' => 'Government Agency',
        ]);

        LocationType::create([
            'sequence' => 8,
            'name' => 'HDB Shophouses',
        ]);

        LocationType::create([
            'sequence' => 9,
            'name' => 'Hospital',
        ]);

        LocationType::create([
            'sequence' => 10,
            'name' => 'Hotel & Hostel',
        ]);

        LocationType::create([
            'sequence' => 11,
            'name' => 'Int. School & Teritary',
        ]);

        LocationType::create([
            'sequence' => 12,
            'name' => 'Others',
        ]);

        LocationType::create([
            'sequence' => 13,
            'name' => 'Recretional',
        ]);

        LocationType::create([
            'sequence' => 14,
            'name' => 'Worker Domitory',
        ]);
    }
}
