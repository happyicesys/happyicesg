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
        $max_id = count(Bomcategory::all()) > 0 ? Bomcategory::max('category_id') : 1001;
        $numbers = preg_replace('/[^0-9]/', '', $max_id);
        $letters = preg_replace('/[^a-zA-Z]/', '', $max_id);
        $numbers += 1;

        return $letters.$numbers;
    }

    // retrieve increament id for bom component()
    public function getBomcomponentIncrement()
    {
        $max_id = count(Bomcomponent::all()) > 0 ? Bomcomponent::max('component_id') : 10001;
        $numbers = preg_replace('/[^0-9]/', '', $max_id);
        $letters = preg_replace('/[^a-zA-Z]/', '', $max_id);
        $numbers += 1;

        return $letters.$numbers;
    }

    // retrieve increament id for bom component()
    public function getBompartIncrement()
    {
        $max_id = count(Bompart::all()) > 0 ? Bompart::max('part_id') : 10001;
        $numbers = preg_replace('/[^0-9]/', '', $max_id);
        $letters = preg_replace('/[^a-zA-Z]/', '', $max_id);
        $numbers += 1;

        return $letters.$numbers;
    }

    // retrieve increament id for bompartconsumable()
    public function getBompartconsumableIncrement()
    {
        $max_id = count(Bompartconsumable::all()) > 0 ? Bompartconsumable::max('partconsumable_id') : 10001;
        $numbers = preg_replace('/[^0-9]/', '', $max_id);
        $letters = preg_replace('/[^a-zA-Z]/', '', $max_id);
        $numbers += 1;

        return $letters.$numbers;
    }

    // retrieve increament id for bom component()
    public function getBommaintenanceIncrement()
    {
        $max_id = count(Bommaintenance::all()) > 0 ? Bommaintenance::max('maintenance_id') : 100001;
        $max_id += 1;

        return $max_id;
    }

}