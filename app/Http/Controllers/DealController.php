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
use App\Uom;
use Log;

class DealController extends Controller
{

    public function getData($transaction_id)
    {
        $deals =  Deal::with(['item.prices', 'item' => function($query) {
                            $query->withoutGlobalScopes();
                        }])
                        ->whereHas('transaction', function($query) use ($transaction_id){
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
        $request->merge(array('dividend' => strstr($qty, '/') ? strstr($qty, '/', true) : $qty));
        $request->merge(array('divisor' => strstr($qty, '/') ? substr($qty, strpos($qty, '/') + 1) : 1));
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
    public function destroyAjax($deal_id)
    {
        $deal = Deal::findOrFail($deal_id);
        $this->dealDeleteSingle($deal);
        $transaction = Transaction::findOrFail($deal->transaction_id);
        // $deals = Deal::whereTransactionId($deal->transaction_id)->get();
        $deal_total = Deal::whereTransactionId($deal->transaction_id)->get()->sum('amount');
        $deal_totalqty = Deal::whereTransactionId($deal->transaction_id)->whereHas('item', function($query) {
            $query->where('is_inventory', true);
        })->get()->sum('qty');
        $uomsObj = Uom::orderBy('sequence', 'desc')->get();

        $qtyJsonTotal = [];
        foreach($uomsObj as $uomObj) {
            $qtyJsonTotal[$uomObj->name] = 0;
        }
        if($transaction->deals()->exists()) {
            foreach($transaction->deals()->whereHas('item', function($query) {
                $query->where('is_active', true)->where('is_inventory', true);
            })->get() as $dealObj) {
                if($dealObj->qty_json) {
                    foreach($uomsObj as $uomObj) {
                        $qtyJsonTotal[$uomObj->name] += $dealObj->qty_json[$uomObj->name];
                    }
                }
            }
        }
        $transaction->qty_json = $qtyJsonTotal;
        $transaction->total = $deal_total;
        $transaction->total_qty = $deal_totalqty;
        $transaction->save();
        return $deal->id . 'has been successfully deleted';
    }


    private function dealSyncOrder($item_id)
    {
        $deals = Deal::where('qty_status', '1')->where('item_id', $item_id);
        $item = Item::findOrFail($item_id);
        if($item->is_inventory === 1) {
            $item->qty_order = $deals->sum('qty');
            $item->save();
        }
    }

    private function dealDeleteSingle($deal)
    {
        $item = Item::findOrFail($deal->item_id);
        $deal->delete();
        if($deal->qty_status == '1'){
            $this->dealSyncOrder($item->id);
        }else if($deal->qty_status == '2'){
            if($item->is_inventory === 1) {
                $item->qty_now += $deal->qty;
                $item->save();
                $this->loggingDebug($item, $deal);
            }
        }
    }

    // Logging purpose
    private function loggingDebug($item, $deal)
    {
        if($item->id === 356) {
            Log::info($deal->transaction_id.', current: '.$item->qty_now.', qty: '.$deal->qty.', before: '.$deal->qty_before.', after: '.$deal->qty_after);
        }

    }
}
