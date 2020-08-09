<?php

use Illuminate\Database\Seeder;
use App\Role;

class MerchandiserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'merchandiser',
            'label' => 'Merchandiser',
            'remark' => '',
        ]);

        Role::create([
            'name' => 'merchandiser_plus',
            'label' => 'Merchandiser +',
            'remark' => '',
        ]);
    }
}
