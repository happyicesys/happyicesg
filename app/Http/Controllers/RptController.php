<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Person;
use App\Transaction;
use DB;
use Auth;
use PDF;
use App\HasProfileAccess;

class RptController extends Controller
{
    use HasProfileAccess;
    /**Auth for all
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('report.index');
    }

    // retrieve daily rpt with/ without search
    public function getDailyRptApi(Request $request)
    {

        return $this->apiTable($request);

    }

    public function generateDailyRec(Request $request)
    {

        return $this->apiRec($request);

    }

    public function getDailyPdf(Request $request)
    {
        // dd($request->all());
        $now = Carbon::now()->format('d-m-Y H:i');
        $data = [];
        $data = $this->apiTable($request);
        $data['now'] = $now;

        // insert the searched result
        $data['transaction_id'] = $request->transaction_id;
        $data['cust_id'] = $request->cust_id;
        $data['company'] = $request->company;
        $data['status'] = $request->status;
        $data['pay_status'] = $request->pay_status;
        $data['paid_by'] = $request->paid_by;
        $data['paid_at'] = $request->paid_at;
        $data['delivery_date'] = $request->delivery_date;
        $data['driver'] = $request->driver;
        $filename = 'DailyRpt_' . $now . '.pdf';
        $pdf = PDF::loadView('report.dailyrpt_pdf', $data);
        $pdf->setPaper('a4');

        // $pdf->setOrientation('landscape');
        return $pdf->download($filename);
    }

    public function generatePerson(Request $request)
    {
        $option = $request->input('cust_choice');

        if ($option) {
            if ($option == 'all') {
                $title = 'Customers(ALL)';
                $people = Person::where('cust_id', 'NOT LIKE', 'H%')
                    ->whereHas('profile', function ($q) {
                        $q->filterUserProfile();
                    })
                    ->get();

                if (count($people) > 0) {
                    Excel::create($title . '_' . Carbon::now()->format('dmYHis'), function ($excel) use ($people) {
                        $excel->sheet('sheet1', function ($sheet) use ($people) {
                            $sheet->setAutoSize(true);
                            $sheet->setColumnFormat(array(
                                'A:T' => '@'
                            ));
                            $sheet->loadView('report.peopleAll_excel', compact('people'));
                        });
                    })->download('xls');

                    Flash::success('Reports successfully generated');

                } else {
                    Flash::error('There is no records for the selection report');
                }
            } else {
                $person = Person::findOrFail($option);

                if ($person) {
                    $title = $person->cust_id . '(' . $person->company . ')';
                    $transactions = Transaction::wherePersonId($person->id)->latest()->get();
                    Excel::create($title . '_' . Carbon::now()->format('dmYHis'), function ($excel) use ($person, $transactions) {
                        $excel->sheet('sheet1', function ($sheet) use ($person, $transactions) {
                            $sheet->setAutoSize(true);
                            $sheet->setColumnFormat(array(
                                'A:T' => '@'
                            ));
                            $sheet->loadView('report.person_excel', compact('person', 'transactions'));
                        });
                    })->download('xls');
                    Flash::success('Reports successfully generated');
                } else {
                    Flash::error('There is no records for the selection report');
                }
            }
        } else {
            Flash::error('Please Select One From the List');
        }
        return redirect('report');
    }

    public function generateTransaction(Request $request)
    {
        $title = 'Transaction';
        $radiobtn = $request->input('choice_transac');
        $datefrom = $request->input('transaction_datefrom');
        $dateto = $request->input('transaction_dateto');
        $year = $request->input('transac_year');
        $month = $request->input('transac_month');


        switch ($radiobtn) {
            case 'tran_specific':
                if ($datefrom && $dateto) {
                    $date1 = $datefrom;
                    $date2 = $dateto;
                    $transactions = Transaction::with('deals', 'person')
                        ->searchDateRange($datefrom, $dateto)
                        ->whereHas('person.profile', function ($q) {
                            $q->filterUserProfile();
                        });
                    $transactions = $transactions->get();
                } else {
                    if ($datefrom) {
                        Flash::error('Please Fill in the Date To');
                    } else if ($dateto) {
                        Flash::error('Please Fill in the Date From');
                    } else {
                        Flash::error('Please Fill in the Searching Dates');
                    }
                }
                break;

            case 'tran_all':
                $date1 = Carbon::parse('first day of January ' . $year)->format('d M y');
                $date2 = Carbon::parse('last day of December ' . $year)->format('d M y');
                $transactions = Transaction::with('deals', 'person')
                    ->searchYearRange($year)
                    ->whereHas('profile', function ($q) {
                        $q->filterUserProfile();
                    });
                $transactions = $transactions->get();
                break;

            case 'tran_month':
                $date1 = Carbon::create(Carbon::now()->year, $month)->startOfMonth()->format('d M y');
                $date2 = Carbon::create(Carbon::now()->year, $month)->endOfMonth()->format('d M y');
                $transactions = Transaction::with('deals', 'person')
                    ->searchMonthRange($month)
                    ->whereHas('profile', function ($q) {
                        $q->filterUserProfile();
                    });
                $transactions = $transactions->get();
                break;

        }

        if (isset($transactions)) {
            if (count($transactions) > 0) {
                Excel::create($title . '_' . Carbon::now()->format('dmYHis'), function ($excel) use ($transactions, $date1, $date2) {
                    $excel->sheet('sheet1', function ($sheet) use ($transactions, $date1, $date2) {
                        $sheet->setColumnFormat(array(
                            'A:P' => '@'
                        ));
                        $sheet->loadView('report.transaction_excel', compact('transactions', 'date1', 'date2'));
                    });
                })->download('xls');
                Flash::success('Reports successfully generated');
            } else {
                Flash::error('There is no records for the selection report');
            }
        }

        return redirect('report');
    }

    public function generateByProduct(Request $request)
    {
        $title = 'ByProduct';
        $datefrom = $request->input('byproduct_datefrom');
        $dateto = $request->input('byproduct_dateto');

        if ($datefrom && $dateto) {
            $date1 = Carbon::createFromFormat('d M y', $datefrom)->format('Y-m-d');
            $date2 = Carbon::createFromFormat('d M y', $dateto)->format('Y-m-d');

            $deals = DB::table('deals')
                ->leftJoin('transactions', 'deals.transaction_id', '=', 'transactions.id')
                ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                ->leftJoin('items', 'deals.item_id', '=', 'items.id')
                ->select('profiles.name as profile_name', 'items.product_id', 'people.cust_id', 'people.company', 'transactions.id', 'transactions.status', 'transactions.delivery_date', 'deals.qty', 'deals.amount')
                ->where('transactions.delivery_date', '>=', $date1)
                ->where('transactions.delivery_date', '<=', $date2)
                ->get();

            if (isset($deals)) {
                if (count($deals) > 0) {
                    Excel::create($title . '_' . Carbon::now()->format('dmYHis'), function ($excel) use ($deals, $datefrom, $dateto) {
                        $excel->sheet('sheet1', function ($sheet) use ($deals, $datefrom, $dateto) {
                            $sheet->setColumnFormat(array(
                                'A:P' => '@'
                            ));
                            $sheet->loadView('report.deal_excel', compact('deals', 'datefrom', 'dateto'));
                        });
                    })->download('xls');
                    Flash::success('Reports successfully generated');
                } else {
                    Flash::error('There is no records for the selection report');
                }
            }
        } else {

            if ($datefrom) {

                Flash::error('Please Fill in the Date To');

            } else if ($dateto) {

                Flash::error('Please Fill in the Date From');

            } else {

                Flash::error('Please Fill in the Searching Dates');
            }

        }
        return redirect('report');
    }

    public function generateDriver(Request $request)
    {

        $datefrom = $request->input('driver_datefrom');
        $dateto = $request->input('driver_dateto');

        // declaring dates
        if ($datefrom) {
            $date1 = Carbon::createFromFormat('d M y', $request->input('driver_datefrom'))->format('Y-m-d');
        }

        if ($dateto) {
            $date2 = Carbon::createFromFormat('d M y', $request->input('driver_dateto'))->format('Y-m-d');
        }


        if ($request->input('driver')) {
            $driver = $request->input('driver');
            $title = 'Driver(' . $driver . ')_';
            if ($datefrom == $dateto) {
                $transactions = DB::table('transactions')
                    ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                    ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                    ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name as profile_name')
                    ->where('transactions.delivery_date', $date1)
                    ->where('transactions.driver', $driver)
                    ->get();
            } else {
                if ($datefrom and $dateto) {
                    $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name as profile_name')
                        ->where('transactions.delivery_date', '>=', $date1)
                        ->where('transactions.delivery_date', '<=', $date2)
                        ->where('transactions.driver', $driver)
                        ->get();
                } else if (($datefrom and !$dateto) or (!$datefrom and $dateto)) {
                    Flash::error('Please Fill in the Date From/ To');

                }
            }
        } else {

            Flash::error('Please Select a Driver');
        }

        if (isset($transactions)) {
            if (count($transactions) > 0) {
                Excel::create($title . '_' . Carbon::now()->format('dmYHis'), function ($excel) use ($transactions, $datefrom, $dateto) {
                    $excel->sheet('sheet1', function ($sheet) use ($transactions, $datefrom, $dateto) {
                        $sheet->setColumnFormat(array(
                            'A:P' => '@'
                        ));
                        $sheet->loadView('report.driver_excel', compact('transactions', 'datefrom', 'dateto'));
                    });
                })->download('xls');
                Flash::success('Reports successfully generated');

            } else {
                Flash::error('There is no records for the selection report');
            }
        }

        return redirect('report');
    }

    public function getVerifyPaid(Request $request, $id = null)
    {
        $checkboxes = $request->checkbox;
        $pay_methods = $request->pay_method;
        $notes = $request->note;

        if ($checkboxes) {
            foreach ($checkboxes as $index => $checkbox) {
                $transaction = Transaction::findOrFail($index);

                if ($transaction->status === 'Delivered' or $transaction->status === 'Verified Owe') {
                    if ($transaction->pay_status === 'Owe') {
                        $transaction->status = 'Verified Owe';
                        $transaction->updated_by = Auth::user()->name;
                        $transaction->save();
                    } else if ($transaction->pay_status === 'Paid') {
                        $transaction->status = 'Verified Paid';
                        if (isset($pay_methods)) {
                            if (array_key_exists($index, $pay_methods)) {
                                $transaction->pay_method = $pay_methods[$index];
                            }
                        }

                        if (isset($pay_methods)) {
                            if (array_key_exists($index, $notes)) {
                                $transaction->note = $notes[$index];
                            }
                        }

                        $transaction->updated_by = Auth::user()->name;
                        $transaction->save();
                    }
                }
            }
        }

        if ($request->input('verify_single')) {
            dd($checkboxes);
        }

        return redirect('report');
    }

    // calculating gst and non for delivered total
    private function calTransactionTotal($arr)
    {
        $total_amount = 0;
        foreach ($arr as $transaction) {
            $person = Person::findOrFail($transaction->person_id);
            $profile = $person->profile;
            $person_gst = $transaction->gst;
            $person_gst_inclusive = $transaction->is_gst_inclusive;

            if ($person_gst == 1 and $person_gst_inclusive == 0) {
                $total_amount += round(($transaction->total * (100 + $transaction->gst_rate) / 100), 2);
            } else {
                $total_amount += $transaction->total;
            }
        }
        return $total_amount;
    }

    // calculating gst and non for delivered total
    private function calDBTransactionTotal($query)
    {
        $total_amount = 0;
        $nonGst_amount = 0;
        $gst_inclusive = 0;
        $gst_exclusive = 0;
        $delivery_fee = 0;
        $query1 = clone $query;
        $query2 = clone $query;
        $query3 = clone $query;
        $query4 = clone $query;

        $nonGst_amount = $query1->where('transactions.gst', 0)->sum(DB::raw('ROUND((transactions.total), 2)'));
        $gst_inclusive = $query2->where('transactions.gst', 1)->where('transactions.is_gst_inclusive', 1)->sum(DB::raw('ROUND(transactions.total, 2)'));
        $gst_exclusive = $query3->where('transactions.gst', 1)->where('transactions.is_gst_inclusive', 0)->sum(DB::raw('ROUND((transactions.total * (100+transactions.gst_rate)/100), 2)'));
        $delivery_fee = $query4->sum(DB::raw('ROUND((transactions.delivery_fee), 2)'));
        $total_amount = $nonGst_amount + $gst_inclusive + $gst_exclusive + $delivery_fee;
        return $total_amount;
    }

    // calculating delivered total
    private function calDelTransaction($query)
    {
        $transactionArr = $query->get();
        $del_amount = 0.00;
        $del_qty = 0.00;
        $del_paid = 0.00;

        foreach($transactionArr as $transaction) {
            $del_amount += $transaction->total;
            $del_qty += $transaction->total_qty;

            if($transaction->pay_status == 'Paid') {
                $del_paid += $transaction->total;
            }
        }

        return [
            'del_amount' => round($del_amount, 2),
            'del_qty' => round($del_qty, 4),
            'del_paid' => round($del_paid, 2)
        ];
    }

    // calculating paid total
    private function calTransaction($query, $request)
    {
        $transactionArr = $query->get();

        $del_amount = 0;
        $del_qty = 0;
        $del_paid = 0;

        $paid_amount = 0;
        $paid_cash = 0;
        $paid_cheque_in = 0;
        $paid_cheque_out = 0;
        $paid_tt = 0;
        $paid_equals = false;

        foreach($transactionArr as $transaction) {
            // dd($transaction->delivery_date, $request->delivery_date);
            if($transaction->delivery_date == $request->delivery_date) {
                $del_amount += $transaction->total;
                $del_qty += $transaction->total_qty;

                if($transaction->pay_status == 'Paid') {
                    $del_paid += $transaction->total;
                }
            }

            if($transaction->paid_at == $request->paid_at) {
                $paid_amount += $transaction->total;

                switch($transaction->pay_method) {
                    case 'cash':
                       $paid_cash += $transaction->total;
                       break;
                    case 'cheque':
                        if($transaction->total >= 0) {
                            $paid_cheque_in += $transaction->total;
                        }else if($transaction->total < 0) {
                            $paid_cheque_out += $transaction->total;
                        }
                       break;
                    case 'tt':
                       $paid_tt += $transaction->total;
                       break;
                }
            }
        }

        $del_amount = round($del_amount, 2);
        $del_qty = round($del_qty, 4);
        $del_paid = round($del_paid, 2);

        $paid_amount = round($paid_amount, 2);
        $paid_cash = round($paid_cash, 2);
        $paid_cheque_in = round($paid_cheque_in, 2);
        $paid_cheque_out = round($paid_cheque_out, 2);
        $paid_tt = round($paid_tt, 2);

        if($paid_amount == $paid_cash + $paid_cheque_in + $paid_cheque_out + $paid_tt) {
            $paid_equals = true;
        }

        return [
            'del_amount' => $del_amount,
            'del_qty' => $del_qty,
            'del_paid' => $del_paid,
            'paid_amount' => $paid_amount,
            'paid_cash' => $paid_cash,
            'paid_cheque_in' => $paid_cheque_in,
            'paid_cheque_out' => $paid_cheque_out,
            'paid_tt' => $paid_tt,
            'paid_equals' => $paid_equals
        ];
    }

    // calculating qty total
    private function calQtyTotal($arr)
    {
        $total_qty = 0;
        foreach ($arr as $transaction) {
            $total_qty += $transaction->total_qty;
        }
        return $total_qty;
    }

    // cal qty total by query
    private function calDBQtyTotal($query)
    {
        $query1 = clone $query;
        $total_qty = 0;
        $total_qty = $query1->sum('transactions.total_qty');
        $total_qty = round(floatval($total_qty), 4);
        return $total_qty;
    }


    // generate union api table for daily rpt
    private function apiTable($request)
    {
        $delivery_date = $request->delivery_date;
        $paid_at = $request->paid_at;
        $paid_by = $request->paid_by;
        $driver = $request->driver;
        $role = $request->role;
        $sortName = $request->sortName;
        $sortBy = $request->sortBy;
        // dd($request->all());

        $query = DB::table('transactions')
            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id');

/*
        if ($delivery_date and $paid_at) {
            $query = $query->whereDate('delivery_date', '=', $delivery_date);
        } */

        if (Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
            $query = $query->whereDriver(Auth::user()->name);
        } else if ($driver and $paid_by) {
            $query = $query->where('driver', 'LIKE', '%' . $driver . '%');
        }

        $query = $this->filterUserDbProfile($query);

        $query = $this->extraField($request, $query);
        $query = $query->select(
            'transactions.id',
            'people.cust_id',
            'people.company',
            'people.id as person_id',
            'transactions.status',
            DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
            'transactions.driver',
            'transactions.total_qty',
            'transactions.pay_status',
            'transactions.updated_by',
            'transactions.updated_at',
            'profiles.name',
            'transactions.created_at',
            'profiles.gst',
            'people.gst_rate',
            'transactions.pay_method',
            'transactions.note',
            'transactions.paid_by',
            DB::raw('DATE(transactions.paid_at) AS paid_at'),
            'transactions.delivery_fee',
            DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                            CASE
                            WHEN transactions.is_gst_inclusive=0
                            THEN total*((100+transactions.gst_rate)/100)
                            ELSE transactions.total
                            END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2) AS total'),
            'profiles.id as profile_id',
            'transactions.is_gst_inclusive'
        );

        $query1 = clone $query;
        $query2 = clone $query;
        $query3 = clone $query;
        $query4 = clone $query;

        $query1 = $query1->where(function ($q) use ($delivery_date) {
            $q->whereIn('status', array('Delivered', 'Verified Owe', 'Verified Paid', 'Confirmed'));
            $q->where('pay_status', 'Paid');
            $q->whereDate('paid_at', '=', $delivery_date);
        });

        $query2 = $query2->where(function ($q) use ($delivery_date) {
            $q->whereIn('status', array('Delivered', 'Verified Owe', 'Verified Paid'));
            $q->where('pay_status', 'Owe');
            $q->whereDate('delivery_date', '=', $delivery_date);
        });

        $query3 = $query3->where(function ($q) use ($delivery_date) {
            $q->whereIn('status', array('Delivered', 'Verified Owe', 'Verified Paid'));
            $q->where('pay_status', 'Paid');
            $q->whereDate('paid_at', '=', $delivery_date);
        });

        $query4 = $query4->where(function ($q) use ($delivery_date){
            $q->where('status', 'Confirmed');
            $q->where('pay_status', 'Paid');
            $q->whereDate('paid_at', '=', $delivery_date);
        });

        $query4 = $query4->union($query1)->union($query2)->union($query3);

        $calquery = clone $query4;

        $calArr = $this->calTransaction($calquery, $request);


        if($sortName){
            // dd($sortName, $sortBy);
            $query4 = $query4->orderBy($sortName, $sortBy ? 'asc' : 'desc');
        }else {
            // dd('default');
            $query4 = $query4->orderBy('id', 'desc');
        }

        $transactions = $query4->get();

        $data = [
            'transactions' => $transactions,
            'del_amount' => $calArr['del_amount'],
            'del_qty' => $calArr['del_qty'],
            'del_paid' => $calArr['del_paid'],
            'paid_amount' => $calArr['paid_amount'],
            'paid_cash' => $calArr['paid_cash'],
            'paid_cheque_in' => $calArr['paid_cheque_in'],
            'paid_cheque_out' => $calArr['paid_cheque_out'],
            'paid_tt' => $calArr['paid_tt'],
            'paid_equals' => $calArr['paid_equals']
        ];

        return $data;
    }

    private function apiRec($request)
    {
        // variable init
        $amt_del = 0;
        $qty_del = 0;
        $paid_del = 0;
        $amt_mod = 0;
        $cash_mod = 0;
        $chequein_mod = 0;
        $chequeout_mod = 0;
        $delivery_date = $request->delivery_date;
        $paid_at = $request->paid_at;
        $paid_by = $request->paid_by;
        $driver = $request->driver;

        $query = DB::table('transactions')
            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id');

        $query1 = clone $query;

        $query1 = $query1->whereIn('status', array('Delivered', 'Verified Owe', 'Verified Paid', 'Confirmed'));

        // check whether delivery_date presence
        if ($delivery_date) {
            $query1 = $query1->where('delivery_date', '=', $delivery_date);
        }

        // if user is driver
        if (Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
            $query1 = $query1->where('driver', Auth::user()->name);
        } else if ($driver) {
            $query1 = $query1->where('driver', 'like', '%' . $driver . '%');
        }

        $query1 = $this->filterUserDbProfile($query1);

        $query1 = $this->extraField($request, $query1);
        $query1 = $query1->orderBy('transactions.id', 'desc');

        $query2 = clone $query;

        if ($paid_at) {
            $query2 = $query2->whereDate('paid_at', '=', $paid_at);
        }

        // if user is driver
        if (Auth::user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
            $query2 = $query2->where('paid_by', Auth::user()->name);
        }

        // if paid_by presence
        if ($paid_by) {
            $query2 = $query2->where('paid_by', 'like', '%' . $paid_by . '%');
        }

        $query2 = $this->filterUserDbProfile($query2);

        $query2 = $this->extraField($request, $query2);
        $query2 = $query2->orderBy('transactions.id', 'desc');
        $amt_del = $this->calDBTransactionTotal($query1);
        $qty_del = $this->calDBQtyTotal($query1);
        $paid_del = $this->calDBTransactionTotal($query1->where('pay_status', '=', 'Paid'));
        $amt_mod = $this->calDBTransactionTotal($query2);
        $cash_mod = $this->payMethodConDB($query2, 'cash');
        $chequein_mod = $this->payMethodConDB($query2, 'cheque', 'in');
        $chequeout_mod = $this->payMethodConDB($query2, 'cheque', 'out');
        $tt_mod = $this->payMethodConDB($query2, 'tt');
        $del_cashmod = $this->payMethodConDBDelivery($query2, 'cash');
        $del_chequemod = $this->payMethodConDBDelivery($query2, 'cheque');
        $delivery_total1 = $this->calDBDeliveryTotal($query1);
        $delivery_paid = $this->calDBDeliveryTotal($query1->where('pay_status', '=', 'Paid'));
        $delivery_total2 = $this->calDBDeliveryTotal($query2);

        $data = [
            'amt_del' => $amt_del /*+ $delivery_total1*/,
            'qty_del' => $qty_del,
            'paid_del' => $paid_del /*+ $delivery_paid*/,
            'amt_mod' => $amt_mod /*+ $delivery_total2*/,
            'cash_mod' => $cash_mod /*+ $del_cashmod*/,
            'chequein_mod' => $chequein_mod /*+ $del_chequemod*/,
            'chequeout_mod' => $chequeout_mod,
            'tt_mod' => $tt_mod
        ];
        return $data;
    }

    private function payMethodCon($transactions, $con)
    {
        $total = 0;
        foreach ($transactions as $transaction) {
            $person = Person::findOrFail($transaction->person_id);
            $profile = $person->profile;
            if ($con === 'cash') {
                if ($transaction->pay_method == 'cash') {
                    $total += $transaction->gst == '1' ? round(($transaction->total * (100 + $transaction->gst_rate) / 100), 2) : $transaction->total;
                }
            } else if ($con === 'cheque') {
                if ($transaction->pay_method == 'cheque') {
                    $total += $transaction->gst == '1' ? round(($transaction->total * (100 + $transaction->gst_rate) / 100), 2) : $transaction->total;
                }
            }
        }
        return $total;
    }
    private function payMethodConDB($query, $con, $chequetype = null)
    {
        $total = 0;
        $query1 = clone $query;
        if ($chequetype) {
            if ($chequetype === 'in') {
                $total = $this->calDBTransactionTotal($query1->where('transactions.pay_method', $con)->where('transactions.total', '>', 0));
            } else if ($chequetype === 'out') {
                $total = $this->calDBTransactionTotal($query1->where('transactions.pay_method', $con)->where('transactions.total', '<', 0));
            }
        } else {
            $total = $this->calDBTransactionTotal($query1->where('transactions.pay_method', $con));
        }
        return $total;
    }

    private function payMethodConDBDelivery($query, $con)
    {
        $q1 = clone $query;
        $delivery = $this->calDBTransactionTotal($q1->where('transactions.pay_method', $con));
        return $delivery;
    }

    private function extraField($request, $query)
    {
        $transaction_id = $request->transaction_id;
        $cust_id = $request->cust_id;
        $company = $request->company;
        $status = $request->status;
        $pay_status = $request->pay_status;
        $profile_id = $request->profile_id;
        $sortName = $request->sortName;
        $sortBy = $request->sortBy;

        // die(var_dump($request->all()));
        if ($transaction_id) {
            $query = $query->where('transactions.id', 'LIKE', '%' . $transaction_id . '%');
        }
        if ($cust_id) {
            $query = $query->where('people.cust_id', 'LIKE', '%' . $cust_id . '%');
        }
        if ($company) {
            $query = $query->where('people.company', 'LIKE', '%' . $company . '%');
        }
        if ($status) {
            $query = $query->where('transactions.status', 'LIKE', '%' . $status . '%');
        }
/*         if ($statuses) {
            // dd('here');
            if (count($statuses) == 1) {
                $statuses = [$statuses];
            }
            $query = $query->whereIn('transactions.status', $statuses);
        } */
        if ($pay_status) {
            $query = $query->where('transactions.pay_status',  $pay_status);
        }
        if ($profile_id) {
            $query = $query->where('profiles.id', $profile_id);
        }

        return $query;
    }

    // calculate delivery total
    private function calDBDeliveryTotal($query)
    {
        $q = clone $query;
        // dd($q->toSql());
        $delivery_fee = $q->sum('transactions.delivery_fee');
        return $delivery_fee;
    }
}