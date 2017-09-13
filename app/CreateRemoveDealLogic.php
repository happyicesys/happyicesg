<?php

namespace App;
use App\Deal;
use Carbon\Carbon;

trait CreateRemoveDealLogic{

    // parse deal id to change the rest
    public function newDealFilter($deal_id)
    {
        // find the deal is the same day or changing the qty_after for later
        $deal = Deal::findOrFail($deal_id);

        $delivery_date = $deal->transaction->delivery_date;

        if(Carbon::parse($deal->created_at)->gt(Carbon::parse($delivery_date))) {
            // $item->qty_now -= $deal->qty;
        }
        dd($deal, $delivery_date);
    }

    // db query builder
    public function removeDealFilter($query)
    {
        $profileIdArr = $this->searchUserProfileId();

        return $query->whereIn('profiles.id', $profileIdArr);
    }

}