<?php

namespace App\Services;
use App\Deal;

class DealService
{
    public function getDealsApi($request)
    {
        $query = Deal::with(
            [
                'transaction' => function($query) use ($request) {
                    if($request->transaction_id) {
                        $query->id($request->transaction_id);
                    }
                    if($request->statuses) {
                        $query->fullStatus($request->statuses);
                    }
                    if($request->pay_status) {
                        $query->payStatus($request->pay_status);
                    }
                    if($request->updated_by) {
                        $query->updatedBy($request->updated_by);
                    }
                }


        , 'item', 'people', 'people.custcategory', 'people.profile']);
    }
}