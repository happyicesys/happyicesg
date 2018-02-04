<?php

namespace App;
use App\Ftransaction;
use App\Bomcategory;
use App\Bomcomponent;
use App\Bompart;
use App\Bommaintenance;

trait GetIncrement{

    // retrieve increment id for the ftransaction (int franchisee_id)
    public function getFtransactionIncrement($franchisee_id)
    {
        $ftransactions = Ftransaction::where('franchisee_id', $franchisee_id)->get();
        $max_id = 100001;
        if(count($ftransactions) > 0) {
        	$max_id = Ftransaction::where('franchisee_id', $franchisee_id)->max('ftransaction_id');
        	$max_id = $max_id + 1;
        }

        return $max_id;
    }

    // retrieve increament id for bom category()
    public function getBomcategoryIncrement()
    {
        $max_id = count(Bomcategory::all()) > 0 ? Bomcategory::max('category_id') : 100001;
        $max_id += 1;

        return $max_id;
    }

    // retrieve increament id for bom component()
    public function getBomcomponentIncrement()
    {
        $max_id = count(Bomcomponent::all()) > 0 ? Bomcomponent::max('component_id') : 100001;
        $max_id += 1;

        return $max_id;
    }

    // retrieve increament id for bom component()
    public function getBompartIncrement()
    {
        $max_id = count(Bompart::all()) > 0 ? Bompart::max('part_id') : 100001;
        $max_id += 1;

        return $max_id;
    }

    // retrieve increament id for bom component()
    public function getBommaintenanceIncrement()
    {
        $max_id = count(Bommaintenance::all()) > 0 ? Bommaintenance::max('maintenance_id') : 100001;
        $max_id += 1;

        return $max_id;
    }

}