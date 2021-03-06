<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Transaction;
use App\Person;
use App\Operationdate;
use App\Zone;
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

    // return vending machine page()
    public function getMerchandiserIndex()
    {
/*
        $people =   Person::with(['personassets', 'outletVisits' => function($query) {
            $query->latest('date');
        }, 'outletVisits.creator'])
        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
        ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
        ->where('people.id', '=', 3832)
        ->get(); */

        // dd($people->toArray());
        return view('detailrpt.operation.merchandiser');
    }

    // return vending machine mobile page()
    public function getMerchandiserMobileIndex()
    {
        return view('detailrpt.operation.merchandiser_mobile');
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
        $invoiceDriver = request('invoiceDriver');
        $datesVar = [
            'today' => $date,
            'earliest' => $date,
            'latest' => $date
        ];
        $dataArr = $this->generateOperationWorksheetQuery($datesVar);

        $people = $dataArr['people'];
        $dates = $dataArr['dates'];
        $alldata = $dataArr['alldata'];
        $driver = null;
        if($invoiceDriver and $invoiceDriver != '-1') {
            $driver = $invoiceDriver;
        }

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
                        'del_lng' => $person->del_lng,
                        'driver' => $driver
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

    public function getAllZoneApi()
    {
        $zones = \App\Zone::all();
        return $zones;
    }

    // ops worksheet update zone
    public function updatePersonZone(Request $request)
    {
        $personId = $request->person_id;
        $zoneId = $request->zone_id['id'];
        $zoneName = $request->zone_id['name'];

        $person = Person::findOrFail($personId);
        $person->zone_id = $zoneId;
        $person->save();

        $request->merge(['zone_id' => $zoneId]);
        $request->merge(['zone_name' => $zoneName]);

        return $request;
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
            request()->merge(array('previous' => 'Last 14 days'));
        }else {
            $today = request('chosen_date');
        }

        // get previous logic
        $previous = request('previous');
        switch($previous) {
            case 'Last 5 days':
                $earliest = Carbon::parse($today)->subDays(5);
                break;
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
        $zones = request('zones');
        $tags = request('tags');
        $account_manager = request('account_manager');
        $last_transac_color = request('last_transac_color');
        $outletvisit_date = request('outletvisit_date');
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

        if($last_transac_color) {
            switch($last_transac_color) {
                case 'Blue':
                    $people = $people->whereRaw('DATEDIFF(now(), last.delivery_date) >= 8 AND DATEDIFF(now(), last.delivery_date) < 15');
                    break;
                case 'Red';
                    $people = $people->whereRaw('DATEDIFF(now(), last.delivery_date) >= 15');
                    break;
                case 'BlueRed';
                    $people = $people->whereRaw('DATEDIFF(now(), last.delivery_date) >= 8');
                    break;
            }
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

        if($zones) {
            if (count($zones) == 1) {
                $zones = [$zones];
            }
            $people = $people->whereIn('people.zone_id', $zones);
        }

        if($tags) {
            if (count($tags) == 1) {
                $tags = [$tags];
            }
            $people = $people->whereIn('persontags.id', $tags);
        }

        if($account_manager != '') {
            if($account_manager > 0) {
                $people = $people->where('people.account_manager', $account_manager);
            }else if($account_manager == '-1') {
                $people = $people->whereNotNull('people.account_manager')->where('people.account_manager', '<>', 0);
            }

        }

        if($outletvisit_date) {
            $people = $people->whereDate('outlet_visits.date', '=', $outletvisit_date);
        }

        return $people;
    }

    // generate operation worksheet report(Array $datesVar)
    private function generateOperationWorksheetQuery($datesVar)
    {
        $dates = $this->generateDateRange($datesVar['earliest'], $datesVar['latest']);

        $prevStr = "(
            SELECT x.id AS transaction_id, DATE(x.delivery_date) AS delivery_date, y.id AS person_id, DATE_FORMAT(x.delivery_date, '%a') AS day, x.status, ROUND((CASE WHEN x.gst=1 THEN (
                    CASE
                    WHEN x.is_gst_inclusive=0
                    THEN total*((100+x.gst_rate)/100)
                    ELSE x.total
                    END) ELSE x.total END) + (CASE WHEN x.delivery_fee>0 THEN x.delivery_fee ELSE 0 END)) AS total, ROUND(x.total_qty, 1) AS total_qty
            FROM transactions x
            LEFT JOIN people y ON x.person_id=y.id
            WHERE x.id = (
                SELECT a.id FROM transactions a
                WHERE a.person_id=y.id";

        $last3 = $prevStr;
        $last2 = $prevStr;
        $last = $prevStr;

        $last3 .= " AND (a.status='Delivered' OR a.status='Verified Owe' OR a.status='Verified Paid' OR a.status='Cancelled')
                ORDER BY a.delivery_date DESC, a.created_at DESC
                LIMIT 2,1
                )
            GROUP BY y.id
        ) last3";

        $last2 .= " AND (a.status='Delivered' OR a.status='Verified Owe' OR a.status='Verified Paid' OR a.status='Cancelled')
                ORDER BY a.delivery_date DESC, a.created_at DESC
                LIMIT 1,1
                )
            GROUP BY y.id
        ) last2";

        $last .= " AND (a.status='Delivered' OR a.status='Verified Owe' OR a.status='Verified Paid' OR a.status='Cancelled')
                ORDER BY a.delivery_date DESC, a.created_at DESC
                LIMIT 1
                )
            GROUP BY y.id
        ) last";

        // $last3 = DB::raw($last3);
        // $last2 = DB::raw($last2);
        $last = DB::raw($last);

        $outletVisits = DB::raw( "(
            SELECT DATE_FORMAT(x.date, '%a') AS day, DATE_FORMAT(x.date, '%y-%m-%d') AS date, x.person_id, x.outcome, x.remarks, creator.name AS created_by
            FROM outlet_visits x
            LEFT JOIN users AS creator ON creator.id=x.created_by
            WHERE x.id = (
                SELECT a.id FROM outlet_visits a
                WHERE x.person_id=a.person_id
                ORDER BY a.date DESC, a.created_at DESC
                LIMIT 1
            )
            GROUP BY x.person_id
        ) outlet_visits");

        $people =   Person::leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                    ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                    ->leftJoin($last, 'people.id', '=', 'last.person_id')
                    // ->leftJoin($last2, 'people.id', '=', 'last2.person_id')
                    // ->leftJoin($last3, 'people.id', '=', 'last3.person_id')
                    ->join('persontagattaches', 'persontagattaches.person_id', '=', 'people.id', 'left outer')
                    ->leftJoin('persontags', 'persontags.id', '=', 'persontagattaches.persontag_id')
                    ->leftJoin('zones', 'zones.id', '=', 'people.zone_id')
                    ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'people.account_manager')
                    ->leftJoin($outletVisits, 'people.id', '=', 'outlet_visits.person_id')
                    ->select(
                            'people.id', 'people.id AS person_id', 'people.cust_id', 'people.name AS attn_name', 'people.contact', 'people.company', 'people.del_postcode', 'people.operation_note', 'people.del_address', 'people.del_lat', 'people.del_lng', 'zones.name AS zone_name',
                            DB::raw('SUBSTRING(people.preferred_days, 1, 1) AS monday'),
                            DB::raw('SUBSTRING(people.preferred_days, 3, 1) AS tuesday'),
                            DB::raw('SUBSTRING(people.preferred_days, 5, 1) AS wednesday'),
                            DB::raw('SUBSTRING(people.preferred_days, 7, 1) AS thursday'),
                            DB::raw('SUBSTRING(people.preferred_days, 9, 1) AS friday'),
                            DB::raw('SUBSTRING(people.preferred_days, 11, 1) AS saturday'),
                            DB::raw('SUBSTRING(people.preferred_days, 13, 1) AS sunday'),
                            'people.preferred_days', 'people.area_group', 'people.zone_id', 'people.account_manager',
                            'account_manager.name AS account_manager_name',
                        'profiles.id AS profile_id',
                        'custcategories.id AS custcategory_id', 'custcategories.name AS custcategory', 'custcategories.map_icon_file',
                        'last.transaction_id AS ops_transac', 'last.delivery_date AS ops_deldate', 'last.day AS ops_day', 'last.total AS ops_total', 'last.total_qty AS ops_total_qty',
                        // 'last2.transaction_id AS ops2_transac', 'last2.delivery_date AS ops2_deldate', 'last2.day AS ops2_day', 'last2.total AS ops2_total', 'last2.total_qty AS ops2_total_qty', 'last2.delivery_date AS last2_deldate',
                        // 'last3.transaction_id AS ops3_transac', 'last3.delivery_date AS ops3_deldate', 'last3.day AS ops3_day', 'last3.total AS ops3_total', 'last3.total_qty AS ops3_total_qty', 'last3.delivery_date AS last3_deldate',
/*
                        DB::raw('CASE
                                    WHEN (DATEDIFF(now(), last.delivery_date) >= 8 AND DATEDIFF(now(), last.delivery_date) < 15)
                                    THEN "blue"
                                    WHEN DATEDIFF(now(), last.delivery_date) >= 15
                                    THEN "red"
                                ELSE
                                    "black"
                                END AS last_date_color'), */
                        'outlet_visits.date AS outletvisit_date', 'outlet_visits.day AS outletvisit_day', 'outlet_visits.outcome',
                        DB::raw('CASE
                                    WHEN (DATEDIFF(now(), outlet_visits.date) >= 7 AND DATEDIFF(now(), outlet_visits.date) < 14)
                                    THEN "blue"
                                    WHEN DATEDIFF(now(), outlet_visits.date) >= 14
                                    THEN "red"
                                ELSE
                                    "black"
                                END AS outletvisit_date_color')
/*
                        DB::raw('(CASE WHEN last.status = "Cancelled" THEN "Red" ELSE "Black" END) AS last_color'),
                        DB::raw('(CASE WHEN last2.status = "Cancelled" THEN "Red" ELSE "Black" END) AS last2_color'),
                        DB::raw('(CASE WHEN last3.status = "Cancelled" THEN "Red" ELSE "Black" END) AS last3_color') */
                    );
        $people = $this->peopleOperationWorksheetDBFilter($people, $datesVar);

        // only active customers
        $people = $people->where('active', 'Yes');
        // $people = $people->load(['outletVisits', 'outletVisits.creator']);

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
        }else {
            $people = $people->orderBy('outletvisit_date', 'desc');
        }

        $people = $people->orderBy('del_postcode');

        $pageNum = request('pageNum') ? request('pageNum') : 'All';

        if($pageNum == 'All' or request('excel_all') or request('excel_single')){
            $people = $people->get();
        }else{
            $people = $people->paginate($pageNum);
        }

        if($people) {
            foreach($people as $person) {
                for($i=1; $i<=5; $i++) {
                    $person['last'.$i] = $this->getPersonTransactionHistory($person->person_id, $i);
                }
                $person['future'] = $this->getPersonFutureHistory($person->person_id);
            }
        }

        $alldata = array();

        foreach($people as $index1 => $person) {
            foreach($dates as $index2 => $date) {

                $id = $person->person_id.','.$date;

                $deals =  DB::table('deals')
                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                        // ->whereIn('transaction_id', $transactionsId)
                        ->where('transactions.person_id', $person->person_id)
                        ->whereDate('transactions.delivery_date', '=', $date);

                $qty = clone $deals;
                $total = clone $deals;
                $items = clone $deals;
                $qty = $qty->select(DB::raw('ROUND(SUM(deals.qty), 1) AS qty'))->get();
                $total = $total->select(DB::raw('ROUND(SUM(CASE WHEN transactions.gst=1 THEN(CASE WHEN transactions.is_gst_inclusive=0 THEN deals.amount*((100 + transactions.gst_rate)/100) ELSE deals.amount END) ELSE deals.amount END)) AS total'))->get();
                $items = $items->select('items.product_id', DB::raw('ROUND(SUM(deals.qty), 1) AS qty'))->groupBy('items.id')->get();

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

                $fontColor = 'Black';

                if($color) {
                    switch($color) {
                        case 'Green':
                            $fontColor = 'White';
                            break;
                    }
                }

                $alldata[$index1][$index2] = [
                    'id' => $id,
                    'qty' => $qty,
                    'total' => $total,
                    'items' => $items,
                    'color' => $color,
                    'font_color' => $fontColor,
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

    private function getPersonTransactionHistory($person_id, $position) {
        $indexPosition = $position - 1;
        $positionStr = "";
        $positionStr = " LIMIT ".$indexPosition.", 1";

        $deals =  DB::table('deals')
        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
        ->rightJoin('transactions AS x', 'x.id', '=', 'deals.transaction_id')
        ->where('x.person_id', $person_id)
        ->whereRaw("
            x.id = (SELECT a.id FROM
            transactions a WHERE a.person_id=x.person_id
            AND (a.status='Delivered' OR a.status='Verified Owe' OR a.status='Verified Paid' OR a.status='Cancelled')
            ORDER BY a.delivery_date DESC, a.created_at DESC ". $positionStr.")"
        );

        $transaction = clone $deals;
        $transaction = $transaction->select(
                        'x.id',
                        DB::raw('DATE_FORMAT(x.delivery_date, "%y-%m-%d") AS delivery_date'),
                        DB::raw('DATE(x.delivery_date) AS delivery_date_full'),
                        DB::raw('DATE_FORMAT(x.delivery_date, "%a") AS day'),
                        DB::raw('CASE
                                    WHEN (DATEDIFF(now(), x.delivery_date) >= 8 AND DATEDIFF(now(), x.delivery_date) < 15)
                                    THEN "blue"
                                    WHEN DATEDIFF(now(), x.delivery_date) >= 15
                                    THEN "red"
                                ELSE
                                    "black"
                                END AS date_color'),
                        DB::raw('ROUND((CASE WHEN x.gst=1 THEN (
                                CASE
                                WHEN x.is_gst_inclusive=0
                                THEN total*((100+x.gst_rate)/100)
                                ELSE x.total
                                END) ELSE x.total END) + (CASE WHEN x.delivery_fee>0 THEN x.delivery_fee ELSE 0 END)) AS total'),
                        DB::raw('ROUND(x.total_qty, 1) AS total_qty')
                    )
                    ->groupBy('x.id')
            // dd($transaction->get());
                    ->first();

        $deals = $deals->select(
                    'items.product_id',
                    DB::raw('ROUND(SUM(deals.qty), 1) AS qty')
                )
                ->groupBy('items.id')
                ->get();

        return [
            'transaction' => $transaction,
            'deals' => $deals
        ];
    }

    private function getPersonFutureHistory($person_id)
    {
        $transactions =  DB::table('deals')
        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
        ->leftJoin('transactions AS x', 'x.id', '=', 'deals.transaction_id')
        ->where('x.person_id', $person_id)
        ->whereDate('x.delivery_date', '>=', Carbon::today()->toDateString())
        ->whereRaw("
            x.id = (SELECT a.id FROM
            transactions a WHERE a.person_id=x.person_id
            AND (a.status='Confirmed' OR a.status='Pending')
            ORDER BY a.delivery_date ASC, a.created_at ASC LIMIT 5)"
        )
        ->select(
            'x.id',
            DB::raw('DATE(x.delivery_date) AS delivery_date')
            )
        ->groupBy('x.id')
        ->get();

        return $transactions;
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
