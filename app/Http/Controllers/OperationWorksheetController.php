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
use DB;

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
        return view('detailrpt.operation.index');
    }

    // get operation worksheet api()
    public function getOperationWorksheetIndexApi()
    {
        $dataArr = $this->generateOperationWorksheetQuery();
        return [
            'people' => $dataArr['people'],
            'dates' => $dataArr['dates'],
            'alldata' => $dataArr['alldata']
        ];
    }

    // batch confirm note operation worksheet(int person_id)
    public function updateOperationNoteApi($person_id)
    {
        $operation_note = request('operation_note');
        $person = Person::findOrFail($person_id);
        $person->operation_note = $operation_note;
        $person->save();
    }

    // change operation worksheet color()
    public function changeOperationWorksheetIndexColor()
    {
        $id = request('id');
        $person_id = explode(",", $id)[0];
        $delivery_date = explode(",", $id)[1];
        $exists = false;

        $prevOpsDate = Operationdate::where('person_id', $person_id)->where('delivery_date', $delivery_date)->first();

        if($prevOpsDate) {
            $color = $prevOpsDate->color;

            switch($color) {
                case 'Yellow':
                    $prevOpsDate->color = 'Red';
                    $prevOpsDate->save();
                    $exists = true;
                    break;
                case 'Red':
                    $prevOpsDate->delete();
                    $exists = false;
                    break;
            }
        }else {
            $opsdate = new Operationdate;
            $opsdate->person_id = $person_id;
            $opsdate->delivery_date = $delivery_date;
            $opsdate->color = 'Yellow';
            $opsdate->save();
            $exists = true;
        }

        if($exists) {
            $operationdate = $prevOpsDate ? $prevOpsDate : $opsdate;
        }else {
            $operationdate = null;
        }

        return $operationdate;
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

    // filters for operation worksheet(Array $datesVar, Collection $transactions)
    private function operationWorksheetDBFilter($datesVar, $transactions)
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
            $transactions = $transactions->where('profiles.id', $profile_id);
        }

        if($id_prefix) {
            $transactions = $transactions->where('people.cust_id', 'LIKE', $id_prefix.'%');
        }

        if($custcategory) {
            $transactions = $transactions->where('custcategories.id', $custcategory);
        }

        if($cust_id) {
            $transactions = $transactions->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }

        if($company) {
            $transactions = $transactions->where('people.company', 'LIKE', '%'.$company.'%');
        }

        if($earliest) {
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $earliest);
        }

        if($latest) {
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $latest);
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
        $color = request('color');

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

        if($color) {
            $people = $people->whereHas('operationdates', function($q) use ($color) {
                $q->where('color', $color);
            });
        }

        return $people;
    }

    // filter customers search result (Query $customers, array datesvar)
    private function peopleOperationWorksheetDBFilter($people, $datesvar)
    {
        $profile_id = request('profile_id');
        $id_prefix = request('id_prefix');
        $custcategory = request('custcategory');
        $cust_id = request('cust_id');
        $company = request('company');
        $color = request('color');

        if($profile_id) {
            $people = $people->where('profiles.id', $profile_id);
        }

        if($id_prefix) {
            $people = $people->where('people.cust_id', 'LIKE', $id_prefix.'%');
        }

        if($custcategory) {
            $people = $people->where('custcategories.id', $custcategory);
        }

        if($cust_id) {
            $people = $people->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }

        if($company) {
            $people = $people->where('people.company', 'LIKE', '%'.$company.'%');
        }

        if($color) {
            $people = $people->whereExists(function ($q) use ($datesvar, $color) {
                $q->select('*')
                    ->from('operationdates')
                    ->whereRaw('operationdates.person_id = people.id')
                    ->whereDate('operationdates.delivery_date', '=', $datesvar['today'])
                    ->where('operationdates.color', $color);
            });
        }

        return $people;
    }

    // generate operation worksheet report()
    private function generateOperationWorksheetQuery()
    {
        $dates = [];
        $earliest = '';
        $latest = '';
        if(request()->isMethod('get')) {
            $today = Carbon::today()->toDateString();
            request()->merge(array('previous' => 'Last 7 days'));
        }else {
            $today = request('chosen_date');
        }

        // get previous logic
        $previous = request('previous');
        switch($previous) {
            case 'Last 7 days':
                $earliest = Carbon::parse($today)->subDays(7);
                break;
            case 'Last 14 days':
                $earliest = Carbon::parse($today)->subDays(14);
                break;
            default:
                $earliest = Carbon::parse($today);
        }

        // get future logic
        $future = request('future');
        switch($future) {
            case '2 days' :
                $latest = Carbon::parse($today)->addDays(2);
                break;
            default:
                $latest = Carbon::parse($today);
        }

        $todayStr = Carbon::parse($today);
        $todayStr = $todayStr->toDateString();
        $earliestStr = clone $earliest;
        $earliestStr = $earliestStr->toDateString();
        $latestStr = clone $latest;
        $latestStr = $latestStr->toDateString();

        $datesVar = [
            'today' => $todayStr,
            'earliest' => $earliestStr,
            'latest' => $latestStr
        ];
        // dd($latest, $datesVar['latest']);
        $dates = $this->generateDateRange($earliest->toDateString(), $latest->toDateString());

        $transactions = DB::table('deals')
                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                            ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                            ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                            ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                            ->select(
                                'transactions.id AS transaction_id', 'transactions.delivery_date AS delivery_date',
                                'people.id AS person_id', 'people.cust_id', 'people.name', 'people.company', 'people.del_postcode', 'people.operation_note',
                                'profiles.id AS profile_id',
                                'custcategories.id AS custcategory_id',
                                'items.id AS item_id', 'items.is_inventory'
                            );
        $transactions = $this->operationWorksheetDBFilter($datesVar, $transactions);
        $transactions = $transactions
                            ->where('items.is_inventory', 1)
                            ->groupBy('transactions.id')
                            ->get();

        $transactionsId = [];
        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->transaction_id);
        }

        $people = DB::table('people')
                    ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                    ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                    ->select(
                        'people.id AS person_id', 'people.cust_id', 'people.name', 'people.company', 'people.del_postcode', 'people.operation_note',
                        'profiles.id AS profile_id',
                        'custcategories.id AS custcategory_id', 'custcategories.name AS custcategory'
                    );
        $people = $this->peopleOperationWorksheetDBFilter($people, $datesVar);

        // only active customers
        $people = $people->where('active', 'Yes');

        if(request('sortName')){
            $people = $people->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        $people = $people->orderBy('cust_id');

        $pageNum = request('pageNum') ? request('pageNum') : 100;

        if($pageNum == 'All'){
            $people = $people->get();
        }else{
            $people = $people->paginate($pageNum);
        }

        $alldata = array();

        foreach($people as $index1 => $person) {
            foreach($dates as $index2 => $date) {

                $id = $person->person_id.','.$date;

                $qty =  DB::table('deals')
                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                        ->whereIn('transaction_id', $transactionsId)
                        ->where('transactions.person_id', $person->person_id)
                        ->where('transactions.delivery_date', $date)
                        ->sum('deals.qty');

                $color =  DB::table('operationdates')
                            ->where('person_id', $person->person_id)
                            ->whereDate('delivery_date', '=', $date)
                            ->first()
                            ?
                            DB::table('operationdates')
                            ->where('person_id', $person->person_id)
                            ->whereDate('delivery_date', '=', $date)
                            ->first()
                            ->color
                            :
                            '';


                $alldata[$index1][$index2] = [
                    'id' => $id,
                    'qty' => $qty,
                    'color' => $color
                ];
            }
        }

        return [
            'people' => $people,
            'dates' => $dates,
            'alldata' => $alldata
        ];
    }
}
