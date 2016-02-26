<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Deal;
use App\Transaction;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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

        $deal->delete();

        $transaction = Transaction::findOrFail($deal->transaction_id);

        $deals = Deal::whereTransactionId($deal->transaction_id)->get();

        $deal_total = $deals->sum('amount');

        $deal_totalqty = $deals->sum('qty');

        $transaction->total = $deal_total;

        $transaction->total_qty = $deal_totalqty; 

        $transaction->save();       

        return $deal->id . 'has been successfully deleted';
    }    
}
