<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Transaction;
use App\Person;
use App\Operationdate;
use App\Http\Requests;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;
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

        $datesArr = $this->operationWorksheetDateFilter();

        $datesVar = [
            'today' => $datesArr['today'],
            'earliest' => $datesArr['earliest'],
            'latest' => $datesArr['latest']
        ];

        $dataArr = $this->generateOperationWorksheetQuery($datesVar);

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

        $prevOpsDate = Operationdate::where('person_id', $person_id)->whereDate('delivery_date', '=', $delivery_date)->first();

        if($prevOpsDate) {
            $color = $prevOpsDate->color;

            switch($color) {
                case 'Red':
                case 'Green':
                case 'Orange':
                    $exists = true;
                    break;
                case 'Yellow':
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

    // generate batch invoices
    public function generateBatchInvoices()
    {
        $date = request('chosen_date');
        $datesVar = [
            'today' => $date,
            'earliest' => $date,
            'latest' => $date
        ];
        $dataArr = $this->generateOperationWorksheetQuery($datesVar);

        $people = $dataArr['people'];
        $dates = $dataArr['dates'];
        $alldata = $dataArr['alldata'];

        foreach($people as $indexpeople => $person) {
            foreach($alldata[$indexpeople] as $data) {
                if($data['color'] === 'Yellow') {
                    $transaction = Transaction::create([
                        'delivery_date' => $date,
                        'person_id' => $person->person_id,
                        'status' => 'Pending',
                        'pay_status' => 'Owe',
                        'updated_by' => auth()->user()->name,
                        'created_by' => auth()->user()->id,
                        'del_postcode' => $person->del_postcode,
                        'del_address' => $person->del_address,
                        'del_lat' => $person->del_lat,
                        'del_lng' => $person->del_lng
                    ]);

                    $prevOpsDate = Operationdate::where('person_id', $person->person_id)->whereDate('delivery_date', '=', $date)->first();

                    if($prevOpsDate) {
                        $prevOpsDate->color = 'Orange';
                        $prevOpsDate->save();
                    }else {
                        $opsdate = new Operationdate;
                        $opsdate->person_id = $person_id;
                        $opsdate->delivery_date = $date;
                        $opsdate->color = 'Orange';
                        $opsdate->save();
                    }
                }
            }
        }
    }

    // export excel for operation worksheet ()
    public function exportOperationExcel()
    {
        // dd((bool)request('single'), (bool)request('all'));
        if(request('excel_single')) {
            $today = request('chosen_date');

            $datesVar = [
                'today' => $today,
                'earliest' => $today,
                'latest' => $today
            ];

        } else if (request('excel_all')) {
            $datesArr = $this->operationWorksheetDateFilter();

            $datesVar = [
                'today' => $datesArr['today'],
                'earliest' => $datesArr['earliest'],
                'latest' => $datesArr['latest']
            ];
        }

        $dataArr = $this->generateOperationWorksheetQuery($datesVar);

        $people = $dataArr['people'];
        $dates = $dataArr['dates'];
        $alldata = $dataArr['alldata'];

        $title = 'Operation Worksheet';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($people, $dates, $alldata) {
            $excel->sheet('sheet1', function($sheet) use ($people, $dates, $alldata) {
                $sheet->setColumnFormat(array('A:P' => '@'));
                $sheet->getPageSetup()->setPaperSize('A4');
                $sheet->setAutoSize(true);
                if(request('excel_single')) {
                    $sheet->loadView('detailrpt.operation.opsworksheet_filtered_excel', compact('people', 'dates', 'alldata'));
                }else {
                    $sheet->loadView('detailrpt.operation.operation_worksheet_excel', compact('people', 'dates', 'alldata'));
                }
            });
        })->download('xlsx');
    }

    // update preferred days in ops worksheet()
    public function updateWeekDay()
    {
        $val = request('value');
        $person_id = request('person_id');
        $day = request('day');
        $daysArr = [];

        $person = Person::findOrFail($person_id);
        $dayArr = explode(",", $person->preferred_days);

        switch($day) {
            case 'monday':
                $dayArr[0] = $val;
                break;
            case 'tuesday':
                $dayArr[1] = $val;
                break;
            case 'wednesday':
                $dayArr[2] = $val;
                break;
            case 'thursday':
                $dayArr[3] = $val;
                break;
            case 'friday':
                $dayArr[4] = $val;
                break;
            case 'saturday':
                $dayArr[5] = $val;
                break;
            case 'sunday':
                $dayArr[6] = $val;
                break;
        }
        $dayStr = implode(",", $dayArr);
        $person->preferred_days =$dayStr;
        $person->save();
    }

    // update area group in ops worksheet()
    public function updateAreaGroup()
    {
        $val = request('value');
        $person_id = request('person_id');
        $area = request('area');
        $areaArr = [];

        $person = Person::findOrFail($person_id);
        $areaArr = explode(",", $person->area_group);

        switch($area) {
            case 'west':
                $areaArr[0] = $val;
                break;
            case 'east':
                $areaArr[1] = $val;
                break;
            case 'others':
                $areaArr[2] = $val;
                break;
            case 'sup':
                $areaArr[3] = $val;
                break;
            case 'ops':
                $areaArr[4] = $val;
                break;
            case 'north':
                $areaArr[5] = $val;
                break;
        }

        for($i=0; $i<5; $i++) {
            $areaArr[$i] = isset($areaArr[$i]) ? $areaArr[$i] : 0;
        }

        ksort($areaArr);

        $areaStr = implode(",", $areaArr);
        $person->area_group = $areaStr;
        $person->save();
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
        // $exclude_custcategory = request('exclude_custcategory');
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

        // dd($exclude_custcategory);
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
        $exclude_custcategory = request('exclude_custcategory');
        $cust_id = request('cust_id');
        $company = request('company');
        $status = request('status');
        // $del_postcode = request('del_postcode');
        $today = $datesVar['today'];
        $earliest = $datesVar['earliest'];
        $latest = $datesVar['latest'];

        if($profile_id) {
            $transactions = $transactions->where('profiles.id', $profile_id);
        }
/*
        if($id_prefix) {
            $transactions = $transactions->where('people.cust_id', 'LIKE', $id_prefix.'%');
        } */

        if($id_prefix) {
            $prefixes = $id_prefix;
            if (count($prefixes) == 1) {
                $prefixes = [$prefixes];
            }
            $transactions = $transactions->whereIn('people.cust_id', $prefixes);
        }
/*
        if($custcategory) {
            $transactions = $transactions->where('custcategories.id', $custcategory);
        } */

        if($custcategory) {
            $custcategories = $custcategory;
            if (count($custcategories) == 1) {
                $custcategories = [$custcategories];
            }
            if($exclude_custcategory) {
                $transactions = $transactions->whereNotIn('custcategories.id', $custcategories);
            }else {
                $transactions = $transactions->whereIn('custcategories.id', $custcategories);
            }
        }

        if($cust_id) {
            $transactions = $transactions->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }

        if($company) {
            $transactions = $transactions->where('people.company', 'LIKE', '%'.$company.'%');
        }
/*
        if($del_postcode) {
            $transactions = $transactions->where('transactions.del_postcode', 'LIKE', '%'.$del_postcode.'%');
        }
 */
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

    // request form date filter original()
    private function operationWorksheetDateFilter()
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
            case 'Last 5 days':
                $earliest = Carbon::parse($today)->subDays(5);
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
            case '5 days' :
                $latest = Carbon::parse($today)->addDays(5);
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

        return [
            'today' => $todayStr,
            'earliest' => $earliestStr,
            'latest' => $latestStr
        ];
    }

    // filter customers search result (Query $customers, array datesvar)
    private function peopleOperationWorksheetDBFilter($people, $datesvar)
    {
        $profile_id = request('profile_id');
        $id_prefix = request('id_prefix');
        $custcategory = request('custcategory');
        $exclude_custcategory = request('exclude_custcategory');
        $cust_id = request('cust_id');
        $company = request('company');
        $color = request('color');
        $del_postcode = request('del_postcode');
        $preferred_days = request('preferred_days');
        $area_groups = request('area_groups');
        $tags = request('tags');
        // die(var_dump($preferred_days));

        if($profile_id) {
            $people = $people->where('profiles.id', $profile_id);
        }
/*
        if($id_prefix) {
            $people = $people->where('people.cust_id', 'LIKE', $id_prefix.'%');
        }

        if($custcategory) {
            $people = $people->where('custcategories.id', $custcategory);
        }
 */
        if($id_prefix) {
            $prefixes = $id_prefix;
            if (count($prefixes) == 1) {
                $prefixes = [$prefixes];
            }
            $people = $people->whereIn('people.cust_id', $prefixes);
        }

        if($custcategory) {
            $custcategories = $custcategory;
            if (count($custcategories) == 1) {
                $custcategories = [$custcategories];
            }
            if($exclude_custcategory) {
                $people = $people->whereNotIn('custcategories.id', $custcategories);
            }else {
                $people = $people->whereIn('custcategories.id', $custcategories);
            }
        }

        if($cust_id) {
            $people = $people->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }

        if($company) {
            $people = $people->where('people.company', 'LIKE', '%'.$company.'%');
        }

        if($del_postcode) {
            $people = $people->where('people.del_postcode', 'LIKE', '%'.$del_postcode.'%');
        }


        if($color) {
/*
            if($color == 'Yellow & Green') {
                $people = $people->whereIn('people.id', function ($q) use ($datesvar){
                    $q->select('people.id')
                        ->from('deals')
                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                        ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                        ->leftJoin('operationdates', 'operationdates.person_id', '=', 'people.id')
                        ->where(function($q) use ($datesvar){
                            $q->orWhere(function($q) use ($datesvar){
                                $q->whereDate('operationdates.delivery_date', '=', $datesvar['today'])
                                    ->where('operationdates.color', 'Yellow');
                            })->orWhere(function($q) use ($datesvar) {
                                $q->whereDate('transactions.delivery_date', '=', $datesvar['today'])
                                    ->where('deals.qty', '>', '0');
                            });
                        });
                });

            }else { */
                $people = $people->whereExists(function ($q) use ($datesvar, $color) {
                    $q->select('*')
                        ->from('operationdates')
                        ->whereRaw('operationdates.person_id = people.id')
                        ->whereDate('operationdates.delivery_date', '=', $datesvar['today'])
                        ->where('operationdates.color', $color);
                });
            // }
        }

        if($preferred_days) {
            switch($preferred_days) {
                case 1:
                    $people = $people->where(DB::raw('SUBSTRING(people.preferred_days, 1, 1)'), '1');
                    break;
                case 2:
                    $people = $people->where(DB::raw('SUBSTRING(people.preferred_days, 3, 1)'), '1');
                    break;
                case 3:
                    $people = $people->where(DB::raw('SUBSTRING(people.preferred_days, 5, 1)'), '1');
                    break;
                case 4:
                    $people = $people->where(DB::raw('SUBSTRING(people.preferred_days, 7, 1)'), '1');
                    break;
                case 5:
                    $people = $people->where(DB::raw('SUBSTRING(people.preferred_days, 9, 1)'), '1');
                    break;
                case 6:
                    $people = $people->where(DB::raw('SUBSTRING(people.preferred_days, 11, 1)'), '1');
                    break;
            }
        }

        if($area_groups) {

            if (count($area_groups) == 1) {
                $area_groups = [$area_groups];
            }

            $people = $people->where(function($query) use ($area_groups) {

                foreach($area_groups as $key => $area) {
                    // dd($area[0]);
                    switch($area[0]) {
                        case 1:
                            $query->orWhere(DB::raw('SUBSTRING(people.area_group, 1, 1)'), '1');
                            break;
                        case 2:
                            $query->orWhere(DB::raw('SUBSTRING(people.area_group, 3, 1)'), '1');
                            break;
                        case 3:
                            $query->orWhere(DB::raw('SUBSTRING(people.area_group, 5, 1)'), '1');
                            break;
                        case 4:
                            $query->orWhere(DB::raw('SUBSTRING(people.area_group, 7, 1)'), '1');
                            break;
                        case 5:
                            $query->orWhere(DB::raw('SUBSTRING(people.area_group, 9, 1)'), '1');
                            break;
                        case 6:
                            $query->orWhere(DB::raw('SUBSTRING(people.area_group, 11, 1)'), '1');
                            break;
                    }
                }
            });
        }

        if($tags) {
            if (count($tags) == 1) {
                $tags = [$tags];
            }
            $people = $people->whereIn('persontags.id', $tags);
        }

        return $people;
    }

    // generate operation worksheet report(Array $datesVar)
    private function generateOperationWorksheetQuery($datesVar)
    {
        $dates = $this->generateDateRange($datesVar['earliest'], $datesVar['latest']);

        $transactions = DB::table('deals')
                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                            ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                            ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                            ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                            ->select(
                                'transactions.id AS transaction_id', 'transactions.delivery_date AS delivery_date',
                                'people.id AS person_id', 'people.cust_id', 'people.name', 'people.company', 'people.del_postcode', 'people.operation_note', 'people.del_address',
                                'profiles.id AS profile_id',
                                'custcategories.id AS custcategory_id',
                                'items.id AS item_id', 'items.is_inventory'
                            );
        $transactions = $this->operationWorksheetDBFilter($datesVar, $transactions);
        $transactions = $transactions
                            ->groupBy('transactions.id')
                            ->get();

        $transactionsId = [];
        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->transaction_id);
        }

        $last2 = DB::raw( "(
            SELECT x.id AS transaction_id, DATE(x.delivery_date) AS delivery_date, y.id AS person_id, DATE_FORMAT(x.delivery_date, '%a') AS day, ROUND((CASE WHEN x.gst=1 THEN (
                    CASE
                    WHEN x.is_gst_inclusive=0
                    THEN total*((100+x.gst_rate)/100)
                    ELSE x.total
                    END) ELSE x.total END) + (CASE WHEN x.delivery_fee>0 THEN x.delivery_fee ELSE 0 END), 2) AS total, x.total_qty
            FROM transactions x
            LEFT JOIN people y ON x.person_id=y.id
            WHERE x.id = (
                SELECT a.id FROM transactions a
                WHERE a.person_id=y.id
                AND (a.status='Delivered' OR a.status='Verified Owe' OR a.status='Verified Paid')
                ORDER BY a.delivery_date
                DESC LIMIT 1,1
                )
            GROUP BY y.id
        ) last2");

        $last = DB::raw("(
            SELECT x.id AS transaction_id, DATE(x.delivery_date) AS delivery_date, y.id AS person_id, DATE_FORMAT(x.delivery_date, '%a') AS day, ROUND((CASE WHEN x.gst=1 THEN (
                    CASE
                    WHEN x.is_gst_inclusive=0
                    THEN total*((100+x.gst_rate)/100)
                    ELSE x.total
                    END) ELSE x.total END) + (CASE WHEN x.delivery_fee>0 THEN x.delivery_fee ELSE 0 END), 2) AS total, x.total_qty
            FROM transactions x
            LEFT JOIN people y ON x.person_id=y.id
            WHERE x.id = (
                SELECT a.id FROM transactions a
                WHERE a.person_id=y.id
                AND (a.status='Delivered' OR a.status='Verified Owe' OR a.status='Verified Paid' OR a.status='Cancelled')
                ORDER BY a.delivery_date
                DESC LIMIT 1
                )
            GROUP BY y.id
        ) last");

        $last_deliver_cancel = DB::raw("(
            SELECT x.id AS transaction_id, DATE(x.delivery_date) AS delivery_date, y.id AS person_id, DATE_FORMAT(x.delivery_date, '%a') AS day, ROUND((CASE WHEN x.gst=1 THEN (
                    CASE
                    WHEN x.is_gst_inclusive=0
                    THEN total*((100+x.gst_rate)/100)
                    ELSE x.total
                    END) ELSE x.total END) + (CASE WHEN x.delivery_fee>0 THEN x.delivery_fee ELSE 0 END), 2) AS total, x.total_qty
            FROM transactions x
            LEFT JOIN people y ON x.person_id=y.id
            WHERE x.id = (
                SELECT a.id FROM transactions a
                WHERE a.person_id=y.id
                AND (a.status='Delivered' OR a.status='Verified Owe' OR a.status='Verified Paid' or a.status='Cancelled')
                ORDER BY a.delivery_date
                DESC LIMIT 1
                )
            GROUP BY y.id
        ) last_deliver_cancel");
/*
        $last2 = DB::raw( "(
            SELECT x.id AS transaction_id, DATE(t.delivery_date) AS delivery_date, y.id AS person_id, DATE_FORMAT(x.delivery_date, '%a') AS day, ROUND((CASE WHEN x.gst=1 THEN (
                    CASE
                    WHEN x.is_gst_inclusive=0
                    THEN total*((100+x.gst_rate)/100)
                    ELSE x.total
                    END) ELSE x.total END) + (CASE WHEN x.delivery_fee>0 THEN x.delivery_fee ELSE 0 END), 2) AS total, x.total_qty
            FROM transactions x
            LEFT JOIN people y ON x.person_id=y.id
            LEFT JOIN (
                SELECT id, person_id, delivery_date FROM transactions
                WHERE (status='Delivered' OR status='Verified Owe' OR status='Verified Paid')
                ORDER BY delivery_date
                DESC LIMIT 1,1
            ) t ON t.person_id=y.id
            GROUP BY y.id
        ) last2");

        $last = DB::raw( "(
            SELECT x.id AS transaction_id, DATE(x.delivery_date) AS delivery_date, y.id AS person_id, DATE_FORMAT(x.delivery_date, '%a') AS day, ROUND((CASE WHEN x.gst=1 THEN (
                    CASE
                    WHEN x.is_gst_inclusive=0
                    THEN x.total*((100+x.gst_rate)/100)
                    ELSE x.total
                    END) ELSE x.total END) + (CASE WHEN x.delivery_fee>0 THEN x.delivery_fee ELSE 0 END), 2) AS total, x.total_qty
            FROM transactions x
            LEFT JOIN people y ON x.person_id=y.id
            LEFT JOIN (
                SELECT id, person_id, delivery_date, total, total_qty FROM transactions
                WHERE (status='Delivered' OR status='Verified Owe' OR status='Verified Paid')
                ORDER BY delivery_date DESC LIMIT 1
            ) AS t
            ON t.person_id=y.id
            GROUP BY y.id
        ) last");
*/

        $people =   Person::with('personassets')
                    ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                    ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                    ->leftJoin($last, 'people.id', '=', 'last.person_id')
                    ->leftJoin($last2, 'people.id', '=', 'last2.person_id')
                    ->leftJoin($last_deliver_cancel, 'people.id', '=', 'last_deliver_cancel.person_id')
                    ->join('persontagattaches', 'persontagattaches.person_id', '=', 'people.id', 'left outer')
                    ->leftJoin('persontags', 'persontags.id', '=', 'persontagattaches.persontag_id')
                    ->select(
                            'people.id AS person_id', 'people.cust_id', 'people.name', 'people.company', 'people.del_postcode', 'people.operation_note', 'people.del_address', 'people.del_lat', 'people.del_lng',
                            DB::raw('SUBSTRING(people.preferred_days, 1, 1) AS monday'),
                            DB::raw('SUBSTRING(people.preferred_days, 3, 1) AS tuesday'),
                            DB::raw('SUBSTRING(people.preferred_days, 5, 1) AS wednesday'),
                            DB::raw('SUBSTRING(people.preferred_days, 7, 1) AS thursday'),
                            DB::raw('SUBSTRING(people.preferred_days, 9, 1) AS friday'),
                            DB::raw('SUBSTRING(people.preferred_days, 11, 1) AS saturday'),
                            DB::raw('SUBSTRING(people.preferred_days, 13, 1) AS sunday'),
                            DB::raw('SUBSTRING(people.area_group, 1, 1) AS west'),
                            DB::raw('SUBSTRING(people.area_group, 3, 1) AS east'),
                            DB::raw('SUBSTRING(people.area_group, 5, 1) AS others'),
                            DB::raw('SUBSTRING(people.area_group, 7, 1) AS sup'),
                            DB::raw('SUBSTRING(people.area_group, 9, 1) AS ops'),
                            DB::raw('SUBSTRING(people.area_group, 11, 1) AS north'),
                            'people.preferred_days', 'people.area_group', 'people.zone_id',
                        'profiles.id AS profile_id',
                        'custcategories.id AS custcategory_id', 'custcategories.name AS custcategory',
                        'last.transaction_id AS ops_transac', 'last.delivery_date AS ops_deldate', 'last.day AS ops_day', 'last.total AS ops_total', 'last.total_qty AS ops_total_qty',
                        'last2.transaction_id AS ops2_transac', 'last2.delivery_date AS ops2_deldate', 'last2.day AS ops2_day', 'last2.total AS ops2_total', 'last2.total_qty AS ops2_total_qty', 'last2.delivery_date AS last2_deldate',
                        DB::raw('CASE
                                    WHEN (DATEDIFF(now(), last_deliver_cancel.delivery_date) >= 8 AND DATEDIFF(now(), last_deliver_cancel.delivery_date) < 15)
                                    THEN "blue"
                                    WHEN DATEDIFF(now(), last_deliver_cancel.delivery_date) >= 15
                                    THEN "red"
                                ELSE
                                    "black"
                                END AS last_date_color')
                    );
        $people = $this->peopleOperationWorksheetDBFilter($people, $datesVar);

        // only active customers
        $people = $people->where('active', 'Yes');

        $dtdpeople = clone $people;
        $dtdmember = clone $people;

        // rules for normal exclude D and H code
        $people = $people->where('cust_id', 'NOT LIKE', 'H%')
                        ->where('cust_id', 'NOT LIKE', 'D%');

        // filter H codes who has transactions within the dates
        $dtdpeople = $dtdpeople->where('cust_id', 'LIKE', 'H%')
                                ->whereExists(function($q) use ($datesVar) {
                                    $q->select('*')
                                    ->from('deals')
                                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                    ->whereRaw('transactions.person_id = people.id')
                                    ->whereDate('transactions.delivery_date', '>=', $datesVar['earliest'])
                                    ->whereDate('transactions.delivery_date', '<=', $datesVar['latest'])
                                    ->where('deals.qty', '>', '0');
                                });

        // filter H codes who has transactions within the dates
        $dtdmember = $dtdmember->where('cust_id', 'LIKE', 'D%')
                                ->whereExists(function($q) use ($datesVar) {
                                    $q->select('*')
                                    ->from('deals')
                                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                    ->whereRaw('transactions.person_id = people.id')
                                    ->whereDate('transactions.delivery_date', '>=', $datesVar['earliest'])
                                    ->whereDate('transactions.delivery_date', '<=', $datesVar['latest'])
                                    ->where('deals.qty', '>', '0');
                                });

        // union
        $people = $people->union($dtdpeople)->union($dtdmember);

/*
                $people = $people->where(function($query) {
                    $query->where('people.cust_id', '')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });*/

        if(request('sortName')){
            $people = $people->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        $people = $people->orderBy('del_postcode');

        $pageNum = request('pageNum') ? request('pageNum') : 'All';

        if($pageNum == 'All' or request('excel_all') or request('excel_single')){
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
                        ->whereDate('transactions.delivery_date', '=', $date)
                        ->sum('deals.qty');

                $transactions =  DB::table('transactions')
                        ->where('transactions.person_id', $person->person_id)
                        ->whereDate('transactions.delivery_date', '=', $date)
                        ->get();

                $bool_transaction = count($transactions) > 0 ? true : false;

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
                    'color' => $color,
                    'bool_transaction' => $bool_transaction,
                ];
            }
        }

        return [
            'people' => $people,
            'dates' => $dates,
            'alldata' => $alldata
        ];
    }

    private function operationDatesSync($transaction_id, $newdate = null)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        // operation worksheet management
        $prevOpsDate = Operationdate::where('person_id', $transaction->person->id)->whereDate('delivery_date', '=', $transaction->delivery_date)->first();

        if($prevOpsDate) {
            $opsdate = $prevOpsDate;
        }else {
            $opsdate = new Operationdate;
        }

        switch($transaction->status) {
            case 'Pending':
            case 'Confirmed':
                $opsdate->color = 'Orange';
                break;
            case 'Delivered':
            case 'Verified Owe':
            case 'Verified Paid':
                $opsdate->color = 'Green';
                break;
            case 'Cancelled':
                $opsdate->color = 'Red';
                break;
        }
        $opsdate->person_id = $transaction->person->id;
        if($newdate) {
            $opsdate->delivery_date = $newdate;
        }else {
            $opsdate->delivery_date = $transaction->delivery_date;
        }
        $opsdate->save();
    }
}
