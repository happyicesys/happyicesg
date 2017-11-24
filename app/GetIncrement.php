<?php

namespace App;
use App\Ftransaction;

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

}