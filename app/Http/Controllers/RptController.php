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
        return view('report.index');
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

                    $transactions = Transaction::searchDateRange($datefrom, $dateto)->with('person');

                    $transactions = $transactions->get();

                }else{

                    if($datefrom){

                        Flash::error('Please Fill in the Date To');

                    }else if($dateto){

                        Flash::error('Please Fill in the Date From');
                    }

                }

            break;

            case 'tran_all':

                $date1 = Carbon::parse('first day of January '.$year)->format('d M y');

                $date2 = Carbon::parse('last day of December '.$year)->format('d M y');

                $transactions = Transaction::searchYearRange($year)->with('person');

                $transactions = $transactions->get();

            break;

            case 'tran_month':

                $date1 = Carbon::create(Carbon::now()->year, $month)->startOfMonth()->format('d M y');

                $date2 = Carbon::create(Carbon::now()->year, $month)->endOfMonth()->format('d M y');

                $transactions = Transaction::searchMonthRange($month)->with('person');

                $transactions = $transactions->get();

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
}
