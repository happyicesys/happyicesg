<?php

use Illuminate\Database\Seeder;
use App\Custcategory;

class CustcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$data = array(
    		array('name'=>'1'),
    		array('name'=>'A'),
    		array('name'=>'C'),
    		array('name'=>'D'),
    		array('name'=>'E'),
    		array('name'=>'F'),
    		array('name'=>'G'),
    		array('name'=>'H'),
    		array('name'=>'Q'),
    		array('name'=>'R'),
    		array('name'=>'S'),
    		array('name'=>'t'),
    		array('name'=>'V'),
    		array('name'=>'W'),
    		array('name'=>'Z'),
    	);

    	Custcategory::insert($data);
    }
}
