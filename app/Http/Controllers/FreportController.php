<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Transaction;
use App\Ftransaction;
use App\Person;

class FreportController extends Controller
{
    // detect authed
    public function __construct()
    {
        $this->middleware('auth');
    }

    // retrieve invoice breakdown detail (Formrequest $request)
    public function getInvoiceBreakdownDetail(Request $request)
    {
        $itemsId = [];
        // $latest3ArrId = [];
        $transactionsId = [];
        $status = $request->status;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;

        $transactions = FTransaction::with(['deals', 'deals.item'])->wherePersonId($request->person_id);
        // $allTransactions = clone $transactions;

        if($status) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('transactions.status', $status);
            }
        }
        // $allTransactions = $allTransactions->latest()->get();

        if($delivery_from){
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }
        if($delivery_to){
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }

        $transactions = $transactions->orderBy('created_at', 'desc')->get();

        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->id);
            foreach($transaction->deals as $deal) {
                array_push($itemsId, $deal->item_id);
            }
        }
        $itemsId = array_unique($itemsId);
        $person_id = $request->person_id ? Person::find($request->person_id)->id : null ;

        if($request->export_excel) {
            $this->exportInvoiceBreakdownExcel($request, $transactionsId, $itemsId, $person_id);
        }

        return view('freport.invbreakdown_detail', compact('request' ,'transactionsId', 'itemsId', 'person_id'));
    }
}
