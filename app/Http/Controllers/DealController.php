<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Deal;
use App\Transaction;
use App\Item;

class DealController extends Controller
{

    public function getData($transaction_id)
    {
        $deals =  Deal::with(['item.prices'])->whereHas('transaction', function($query) use ($transaction_id){

            $query->where('transaction_id', $transaction_id);

        })->get();

        return $deals;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DealRequest $request)
    {

        $input = $request->all();

        $deal = Deal::create($input);

        return Redirect::action('TransactionController@edit', $request->transaction_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return json
     */
    public function destroyAjax($id)
    {
        $deal = Deal::findOrFail($id);

        $item = Item::findOrFail($deal->item_id);

        // revert back the inventory once inv was added
        if($deal->qty_status == 1){

            $deal->qty_status = 3;

        }else if($deal->qty_status == 2){

            $item->qty_now += $deal->qty;
        }

        $deal->delete();

        $item->qty_order = $this->syncDealOrder($item->id);

        $item->save();

        $transaction = Transaction::findOrFail($deal->transaction_id);

        $deals = Deal::whereTransactionId($deal->transaction_id)->get();

        $deal_total = $deals->sum('amount');

        $deal_totalqty = $deals->sum('qty');

        $transaction->total = $deal_total;

        $transaction->total_qty = $deal_totalqty;

        $transaction->save();

        return $deal->id . 'has been successfully deleted';
    }


    private function syncDealOrder($item_id)
    {
        $deals = Deal::where('qty_status', '1')->where('item_id', $item_id);

        return $deals->sum('qty');
    }

    private function syncDealActual($item_id)
    {
        $deals = Deal::where('qty_status', '1')->where('item_id', $item_id);

        return $deals->sum('qty');
    }
}
