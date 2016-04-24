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

class RptController extends Controller
{
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
        // For this Delivered Date start
        // variable init
        $amt_del = 0;

        $qty_del = 0;

        $del_paid = '';

        $del_today = Transaction::where('delivery_date', '=', Carbon::now()->format('Y-m-d'));

        // if user is driver start
        if(Auth::user()->hasRole('driver')){

           $del_today = $del_today->where('driver', Auth::user()->name);
        }

        $del_today = $del_today->get();

        // total for Delivered
        $amt_del = $this->calTransactionTotal($del_today);

        // total of all qty given the condition
        $qty_del = $del_today->sum('total_qty');

        $del_paid = Transaction::where('delivery_date', '=', Carbon::now()->format('Y-m-d'))->where('pay_status', 'Paid')->get();

        $del_paid = $this->calTransactionTotal($del_paid);
        // For this Delivered Date end

        // For this Modified Date start



        // For this Modified Date end


        return view('report.index', compact('amt_del', 'qty_del', 'del_paid'));
    }

    public function generatePerson(Request $request)
    {
        $option = $request->input('cust_choice');

        if($option){

            if($option == 'all'){

                $title = 'Customers(ALL)';

                $people = Person::all();

                if(count($people)>0){

                    Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($people) {

                        $excel->sheet('sheet1', function($sheet) use ($people) {

                            $sheet->setAutoSize(true);

                            $sheet->setColumnFormat(array(
                                'A:T' => '@'
                            ));

                            $sheet->loadView('report.peopleAll_excel', compact('people'));

                        });

                    })->download('xls');

                    Flash::success('Reports successfully generated');


                }else{

                    Flash::error('There is no records for the selection report');

                }
            }else{

                $person = Person::findOrFail($option)->first();

                if($person){

                    $title = $person->cust_id.'('.$person->company.')';

                    $transactions = Transaction::wherePersonId($person->id)->latest()->get();

                    Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($person, $transactions) {

                        $excel->sheet('sheet1', function($sheet) use ($person, $transactions) {

                            $sheet->setAutoSize(true);

                            $sheet->setColumnFormat(array(
                                'A:T' => '@'
                            ));

                            $sheet->loadView('report.person_excel', compact('person', 'transactions'));

                        });

                    })->download('xls');

                    Flash::success('Reports successfully generated');

                }else{

                    Flash::error('There is no records for the selection report');

                }

            }

        }else{

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


        switch($radiobtn){

            case 'tran_specific':

                if($datefrom && $dateto){

                    $date1 = $datefrom;

                    $date2 = $dateto;

                    $transactions = Transaction::with('deals', 'person')->searchDateRange($datefrom, $dateto);

                    $transactions = $transactions->get();
/*
                    $deals = DB::table('deals')
                                ->leftJoin('transactions', 'deals.transaction_id', '=', 'transactions.id')
                                ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                                ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                                ->leftJoin('items', 'deals.item_id', '=', 'items.id')
                                ->select('profiles.name as profile_name', 'items.product_id', 'people.cust_id', 'people.company', 'transactions.id', 'transactions.delivery_date', 'deals.qty', 'deals.amount');

                    $deals->searchDateRange($datefrom, $dateto)->get();*/

                }else{

                    if($datefrom){

                        Flash::error('Please Fill in the Date To');

                    }else if($dateto){

                        Flash::error('Please Fill in the Date From');

                    }else{

                        Flash::error('Please Fill in the Searching Dates');
                    }

                }

            break;

            case 'tran_all':

                $date1 = Carbon::parse('first day of January '.$year)->format('d M y');

                $date2 = Carbon::parse('last day of December '.$year)->format('d M y');

                $transactions = Transaction::with('deals', 'person')->searchYearRange($year);

                $transactions = $transactions->get();
/*                $deals = DB::table('deals')
                            ->leftJoin('transactions', 'deals.transaction_id', '=', 'transactions.id')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->leftJoin('items', 'deals.item_id', '=', 'items.id')
                            ->select('profiles.name as profile_name', 'items.product_id', 'people.cust_id', 'people.company', 'transactions.id', 'transactions.delivery_date', 'deals.qty', 'deals.amount')
                            ->whereBetween('transactions.delivery_date', array($date1, $date2))
                            ->get();*/


            break;

            case 'tran_month':

                $date1 = Carbon::create(Carbon::now()->year, $month)->startOfMonth()->format('d M y');

                $date2 = Carbon::create(Carbon::now()->year, $month)->endOfMonth()->format('d M y');

                $transactions = Transaction::with('deals', 'person')->searchMonthRange($month);

                $transactions = $transactions->get();
/*                $deals = DB::table('deals')
                            ->leftJoin('transactions', 'deals.transaction_id', '=', 'transactions.id')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->leftJoin('items', 'deals.item_id', '=', 'items.id')
                            ->select('profiles.name as profile_name', 'items.product_id', 'people.cust_id', 'people.company', 'transactions.id', 'transactions.delivery_date', 'deals.qty', 'deals.amount');

                $deals->searchDateRange($datefrom, $dateto)->get();*/


            break;

        }

        if(isset($transactions)){

            if(count($transactions)>0){

                Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($transactions, $date1, $date2) {

                    $excel->sheet('sheet1', function($sheet) use ($transactions, $date1, $date2) {

                        $sheet->setColumnFormat(array(
                            'A:P' => '@'
                        ));

                        $sheet->loadView('report.transaction_excel', compact('transactions', 'date1', 'date2'));

                    });

                })->download('xls');

                Flash::success('Reports successfully generated');

            }else{

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

        if($datefrom && $dateto){

            $date1 = Carbon::createFromFormat('d M y', $datefrom)->format('Y-m-d');

            $date2 = Carbon::createFromFormat('d M y', $dateto)->format('Y-m-d');
/*
            $transactions = Transaction::with('deals', 'person')->searchDateRange($datefrom, $dateto);

            $transactions = $transactions->get();*/

            $deals = DB::table('deals')
                        ->leftJoin('transactions', 'deals.transaction_id', '=', 'transactions.id')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('items', 'deals.item_id', '=', 'items.id')
                        ->select('profiles.name as profile_name', 'items.product_id', 'people.cust_id', 'people.company', 'transactions.id', 'transactions.delivery_date', 'deals.qty', 'deals.amount')
                        ->where('transactions.delivery_date', '>=', $date1)
                        ->where('transactions.delivery_date', '<=', $date2)
                        ->get();

            if(isset($deals)){

                if(count($deals)>0){

                    Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($deals, $datefrom, $dateto) {

                        $excel->sheet('sheet1', function($sheet) use ($deals, $datefrom, $dateto) {

                            $sheet->setColumnFormat(array(
                                'A:P' => '@'
                            ));

                            $sheet->loadView('report.deal_excel', compact('deals', 'datefrom', 'dateto'));

                        });

                    })->download('xls');

                    Flash::success('Reports successfully generated');

                }else{

                    Flash::error('There is no records for the selection report');

                }
            }

        }else{

            if($datefrom){

                Flash::error('Please Fill in the Date To');

            }else if($dateto){

                Flash::error('Please Fill in the Date From');

            }else{

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
        if($datefrom){

            $date1 = Carbon::createFromFormat('d M y', $request->input('driver_datefrom'))->format('Y-m-d');
        }

        if($dateto){

            $date2 = Carbon::createFromFormat('d M y', $request->input('driver_dateto'))->format('Y-m-d');
        }


        if($request->input('driver')){

            $driver = $request->input('driver');

            $title = 'Driver('.$driver.')_';

            if($datefrom == $dateto){

                $transactions = DB::table('transactions')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name as profile_name')
                            ->where('transactions.delivery_date', $date1)
                            ->where('transactions.driver', $driver)
                            ->get();

            }else{

                if($datefrom and $dateto){

                    $transactions = DB::table('transactions')
                                ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                                ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                                ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name as profile_name')
                                ->where('transactions.delivery_date', '>=', $date1)
                                ->where('transactions.delivery_date', '<=', $date2)
                                ->where('transactions.driver', $driver)
                                ->get();

                }else if(($datefrom and !$dateto)or(!$datefrom and $dateto)){

                Flash::error('Please Fill in the Date From/ To');

                }
            }
        }else{

            Flash::error('Please Select a Driver');
        }

            if(isset($transactions)){

                if(count($transactions)>0){

                    Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($transactions, $datefrom, $dateto) {

                        $excel->sheet('sheet1', function($sheet) use ($transactions, $datefrom, $dateto) {

                            $sheet->setColumnFormat(array(
                                'A:P' => '@'
                            ));

                            $sheet->loadView('report.driver_excel', compact('transactions', 'datefrom', 'dateto'));

                        });

                    })->download('xls');

                    Flash::success('Reports successfully generated');

                }else{

                    Flash::error('There is no records for the selection report');

                }
            }

        return redirect('report');
    }

    // calculating gst and non for delivered total
    private function calTransactionTotal($arr)
    {
        $total_amount = 0;

        foreach($arr as $transaction){

            $person_gst = Person::findOrFail($transaction->person_id)->profile->gst;

            if($person_gst){

                $total_amount += number_format(($transaction->total * 107/100), 2, '.', ',');

            }else{

                $total_amount += $transaction->total;
            }
        }

        return $total_amount;
    }
}
