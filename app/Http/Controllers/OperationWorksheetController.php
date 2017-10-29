<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Transaction;
use App\Person;
use App\Http\Requests;
use Carbon\Carbon;
use Datetime;
use DateInterval;
use DatePeriod;

class OperationWorksheetController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return vending machine page()
    public function getOperationWorksheetIndex(Request $request)
    {
        $dates = [];
        $earliest = '';
        $latest = '';
        if($request->isMethod('get')) {
            $today = Carbon::today();
            $request->merge(['previous' => 'Last 7 days']);
        }else {
            $today = Carbon::parse($request->choosen_date);
        }

        // get previous logic
        $previous = $request->previous;
        switch($previous) {
            case 'Last 7 days':
                $earliest = (clone $today)->subDays(7);
                break;
            case 'Last 14 days':
                $earliest = (clone $today)->subDays(14);
                break;
            default:
                $earliest = clone $today;
        }

        // get future logic
        $future = $request->future;
        switch($future) {
            case '2 days' :
                $latest = (clone $today)->addDays(2);
            default:
                $latest = (clone $today);
        }

        $datesVar = [
            'today' => (clone $today)->toDateString(),
            'earliest' => (clone $earliest)->toDateString(),
            'latest' => (clone $latest)->toDateString()
        ];

        $dates = $this->generateDateRange($earliest->toDateString(), $latest->toDateString());

        $transactions = Transaction::with(['person', 'person.profile']);
        $transactions = $this->operationWorksheetFilter($request, $datesVar, $transactions);
        $transactions = $transactions->get();

        $transactionsId = [];
        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->id);
        }

        $people = new Person;
        $people = $this->peopleOperationWorksheetFilter($request, $people);
        $people = $people
                        ->whereHas('transactions', function($q) use ($datesVar) {
                            $q->whereDate('delivery_date', '>=', $datesVar['earliest']);
                            $q->whereDate('delivery_date', '<=', $datesVar['latest']);
                        })
                        ->orderBy('cust_id')
                        ->get();
                        // dd($datesVar['earliest'], $datesVar['latest'], $customers->toArray());

        // $allTransactions = $allTransactions->latest()->get();
/*
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
        }*/
        return view('detailrpt.operation.index', compact('request', 'dates', 'transactionsId', 'people'));
    }

    // return each of the dates in array given start and end (String $startDate, String $endDate)
    private function generateDateRange($startDate, $endDate)
    {
        $begin = new DateTime($startDate);

        $end = new DateTime($endDate);

        $interval = new DateInterval('P1D'); // 1 Day
        $dateRange = new DatePeriod($begin, $interval, $end);

        $range = [];
        foreach ($dateRange as $date) {
            $range[] = $date->format('Y-m-d');
        }

        return $range;
    }

    // filters for operation worksheet(Request $request, Array $datesVar, Collection $transactions)
    private function operationWorksheetFilter($request, $datesVar, $transactions)
    {
        $profile_id = $request->profile_id;
        $id_prefix = $request->id_prefix;
        $custcategory = $request->custcategory;
        $cust_id = $request->cust_id;
        $company = $request->company;
        $status = $request->status;
        $today = $datesVar['today'];
        $earliest = $datesVar['earliest'];
        $latest = $datesVar['latest'];


        if($profile_id) {
            $transactions = $transactions->whereHas('people', function($q) use ($profile_id) {
                $q->whereHas('profile', function($q) use ($profile_id) {
                    $q->where('id', $profile_id);
                });
            });
        }

        if($id_prefix) {
            $transactions = $transactions->whereHas('people', function($q) use ($id_prefix) {
                $q->where('cust_id', 'LIKE', $id_prefix.'%');
            });
        }

        if($custcategory) {
            $transactions = $transactions->whereHas('people', function($q) use ($custcategory) {
                $q->whereHas('custcategory', function($q) use ($custcategory) {
                    $q->where('id', $custcategory);
                });
            });
        }

        if($cust_id) {
            $transactions = $transactions->whereHas('people', function($q) use ($cust_id) {
                $q->where('cust_id', 'LIKE', '%'.$cust_id.'%');
            });
        }

        if($company) {
            $transactions = $transactions->whereHas('people', function($q) use ($company) {
                $q->where('company', 'LIKE', '%'.$company.'%');
            });
        }

        if($status) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('transactions.status', $status);
            }
        }

        if($earliest) {
            $transactions = $transactions->whereDate('delivery_date', '>=', $earliest);
        }

        if($latest) {
            $transactions = $transactions->whereDate('delivery_date', '<=', $latest);
        }

        return $transactions;
    }

    // filter customers search result (Request $request, Query $customers)
    private function peopleOperationWorksheetFilter($request, $people)
    {
        $profile_id = $request->profile_id;
        $id_prefix = $request->id_prefix;
        $custcategory = $request->custcategory;
        $cust_id = $request->cust_id;
        $company = $request->company;

        if($profile_id) {
            $people = $people->where('id', $profile_id);
        }

        if($id_prefix) {
            $people = $people->where('cust_id', 'LIKE', $id_prefix.'%');
        }

        if($custcategory) {
            $people = $people->whereHas('custcategory', function($q) use ($custcategory) {
                $q->where('id', $custcategory);
            });
        }

        if($cust_id) {
            $people = $people->where('cust_id', 'LIKE', '%'.$cust_id.'%');
        }

        if($company) {
            $people = $people->where('company', 'LIKE', '%'.$company.'%');
        }

        return $people;
    }
}
