<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Transaction;
use App\Ftransaction;
use App\Person;
use Carbon\Carbon;

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
        $ftransactionsId = [];
        $transactionsId = [];

        $ftransactions = FTransaction::with(['fdeals', 'fdeals.item'])->wherePersonId($request->person_id);
        $ftransactions = $this->filterInvoiceBreakdown($ftransactions);
        $ftransactions = $ftransactions->orderBy('created_at', 'desc')->get();

        foreach($ftransactions as $ftransaction) {
            array_push($ftransactionsId, $ftransaction->id);
            foreach($ftransaction->fdeals as $fdeal) {
                array_push($itemsId, $fdeal->item_id);
            }
        }

        $transactions = Transaction::with(['deals', 'deals.item'])->wherePersonId($request->person_id);
        $transactions = $this->filterInvoiceBreakdown($transactions);
        $transactions = $transactions->get();

        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->id);
        }

        $itemsId = array_unique($itemsId);
        $person_id = $request->person_id ? Person::find($request->person_id)->id : null ;

        if($request->export_excel) {
            $this->exportInvoiceBreakdownExcel($request, $ftransactionsId, $transactionsId, $itemsId, $person_id);
        }

        return view('freport.invbreakdown_detail', compact('request' ,'ftransactionsId', 'transactionsId', 'itemsId', 'person_id'));
    }

    // filter for transactions and ftransactions($query)
    private function filterInvoiceBreakdown($transactions)
    {
        $status = request('status');
        $delivery_from = request('delivery_from');
        $delivery_to = request('delivery_to');

        if($status) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('status', 'Delivered')->orWhere('status', 'Verified Owe')->orWhere('status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('status', $status);
            }
        }
        // $allTransactions = $allTransactions->latest()->get();

        if($delivery_from){
            $transactions = $transactions->whereDate('delivery_date', '>=', $delivery_from);
        }
        if($delivery_to){
            $transactions = $transactions->whereDate('delivery_date', '<=', $delivery_to);
        }

        return $transactions;
    }

    // export excel for invoice breakdown (Formrequest $request, Array $ftransactionsId, Array $transactionsId, Array itemsId, int person_id)
    private function exportInvoiceBreakdownExcel($request, $ftransactionsId, $transactionsId, $itemsId, $person_id)
    {
        $person = Person::findOrFail($person_id);
        $title = 'Franchisee Invoice Breakdown ('.$person->cust_id.')';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($request, $ftransactionsId, $transactionsId, $itemsId, $person_id) {
            $excel->sheet('sheet1', function($sheet) use ($request, $ftransactionsId, $transactionsId, $itemsId, $person_id) {
                $sheet->setColumnFormat(array('A:P' => '@'));
                $sheet->getPageSetup()->setPaperSize('A4');
                $sheet->setAutoSize(true);
                $sheet->loadView('franchisee.invoicebreakdown_excel', compact('request', 'ftransactionsId', 'transactionsId', 'itemsId', 'person_id'));
            });
        })->download('xlsx');
    }
}
