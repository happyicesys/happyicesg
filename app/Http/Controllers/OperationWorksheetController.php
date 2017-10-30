<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Transaction;
use App\Person;
use App\Operationdate;
use App\Http\Requests;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
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
    public function getOperationWorksheetIndex()
    {
        $dates = [];
        $earliest = '';
        $latest = '';
        if(request()->isMethod('get')) {
            $today = Carbon::today();
            request()->merge(array('previous' => 'Last 7 days'));
        }else {
            $today = Carbon::parse(request('choosen_date'));
        }

        // get previous logic
        $previous = request('previous');
        switch($previous) {
            case 'Last 7 days':
                $earliest = clone $today;
                $earliest = $earliest->subDays(7);
                break;
            case 'Last 14 days':
                $earliest = clone $today;
                $earliest = $earliest->subDays(14);
                break;
            default:
                $earliest = clone $today;
        }

        // get future logic
        $future = request('future');
        switch($future) {
            case '2 days' :
                $latest = clone $today;
                $latest = $latest->addDays(2);
                break;
            default:
                $latest = clone $today;
        }

        $todayStr = clone $today;
        $todayStr = $todayStr->toDateString();
        $earliestStr = clone $today;
        $earliestStr = $earliestStr->toDateString();
        $latestStr = clone $today;
        $latestStr = $latestStr->toDateString();

        $datesVar = [
            'today' => $todayStr,
            'earliest' => $earliestStr,
            'latest' => $latestStr
        ];
        // dd($latest, $datesVar['latest']);
        $dates = $this->generateDateRange($earliest->toDateString(), $latest->toDateString());

        $transactions = Transaction::with(['person', 'person.profile']);
        $transactions = $this->operationWorksheetFilter($datesVar, $transactions);
        $transactions = $transactions
                        ->whereHas('deals', function($q) {
                            $q->whereHas('item', function($q) {
                                $q->where('is_inventory', 1);
                            });
                        })
                        ->get();

        $transactionsId = [];
        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->id);
        }

        $people = new Person;
        $people = $this->peopleOperationWorksheetFilter($people);
        $people = $people
                        ->whereHas('transactions', function($q) use ($datesVar) {
                            $q->whereDate('delivery_date', '>=', $datesVar['earliest']);
                            $q->whereDate('delivery_date', '<=', $datesVar['latest']);
                            $q->whereHas('deals', function($q) {
                                $q->whereHas('item', function($q) {
                                    $q->where('is_inventory', 1);
                                });
                            });
                        })
                        ->orderBy('cust_id')
                        ->get();
/*
        if(request('export_excel')) {
            $this->exportInvoiceBreakdownExcel($transactionsId, $itemsId, $person_id);
        }*/
        return view('detailrpt.operation.index', compact('dates', 'transactionsId', 'people'));
    }

    // batch confirm operation worksheet()
    public function batchConfirmOperationWorksheet()
    {
        $checkboxes = request('checkboxes');
        $selectcolors = request('selectcolors');
        $operation_notes = request('operation_notes');

        if($checkboxes) {
            foreach($checkboxes as $index => $checkbox) {
                $person = Person::findOrFail($index);
                $person->operation_note = $operation_notes[$index];
                $person->save();

                foreach($selectcolors as $index2 => $selectcolor) {
                    $person_id = explode('=', $index2)[0];
                    if($person_id == $index) {
                        $year = explode('=', $index2)[1];
                        $month = explode('=', $index2)[2];
                        $day = explode('=', $index2)[3];
                        $date = $year.'-'.$month.'-'.$day;

                        $prevopdate = Operationdate::where('person_id', $index)->whereDate('delivery_date', '=', $date)->first();

                        if($prevopdate) {
                            if($prevopdate->color != $selectcolor) {

                                if($selectcolor == '') {
                                    $prevopdate->delete();
                                }else {
                                    $prevopdate->color = $selectcolor;
                                    $prevopdate->save();
                                }
                            }
                        }else if(!$prevopdate and $selectcolor != '') {
                            $operationdate = new Operationdate;
                            $operationdate->person_id = $index;
                            $operationdate->delivery_date = $date;
                            $operationdate->color = $selectcolor;
                            $operationdate->save();
                        }
                    }
                }
            }
        }else {
            Flash::error('Please select at least one checkbox');
        }
        return redirect()->action('OperationWorksheetController@getOperationWorksheetIndex');
    }

    // return each of the dates in array given start and end (String $startDate, String $endDate)
    private function generateDateRange($startDate, $endDate)
    {
        $begin = new DateTime($startDate);

        $endDate = Carbon::parse($endDate)->addDay()->toDateString();
        $end = new DateTime($endDate);

        $interval = new DateInterval('P1D'); // 1 Day
        $dateRange = new DatePeriod($begin, $interval, $end);

        $range = [];
        foreach ($dateRange as $date) {
            $range[] = $date->format('Y-m-d');
        }

        return $range;
    }

    // filters for operation worksheet(Array $datesVar, Collection $transactions)
    private function operationWorksheetFilter($datesVar, $transactions)
    {
        $profile_id = request('profile_id');
        $id_prefix = request('id_prefix');
        $custcategory = request('custcategory');
        $cust_id = request('cust_id');
        $company = request('company');
        $status = request('status');
        $today = $datesVar['today'];
        $earliest = $datesVar['earliest'];
        $latest = $datesVar['latest'];


        if($profile_id) {
            $transactions = $transactions->whereHas('person', function($q) use ($profile_id) {
                $q->whereHas('profile', function($q) use ($profile_id) {
                    $q->where('id', $profile_id);
                });
            });
        }

        if($id_prefix) {
            $transactions = $transactions->whereHas('person', function($q) use ($id_prefix) {
                $q->where('cust_id', 'LIKE', $id_prefix.'%');
            });
        }

        if($custcategory) {
            $transactions = $transactions->whereHas('person', function($q) use ($custcategory) {
                $q->whereHas('custcategory', function($q) use ($custcategory) {
                    $q->where('id', $custcategory);
                });
            });
        }

        if($cust_id) {
            $transactions = $transactions->whereHas('person', function($q) use ($cust_id) {
                $q->where('cust_id', 'LIKE', '%'.$cust_id.'%');
            });
        }

        if($company) {
            $transactions = $transactions->whereHas('person', function($q) use ($company) {
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

    // filter customers search result (Query $customers)
    private function peopleOperationWorksheetFilter($people)
    {
        $profile_id = request('profile_id');
        $id_prefix = request('id_prefix');
        $custcategory = request('custcategory');
        $cust_id = request('cust_id');
        $company = request('company');

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
