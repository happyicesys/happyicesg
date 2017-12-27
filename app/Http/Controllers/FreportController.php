<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Transaction;
use App\Ftransaction;
use App\Person;
use Carbon\Carbon;
use App\HasProfileAccess;
use DB;

class FreportController extends Controller
{
    use HasProfileAccess;
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
        $ftransactionsId = [];
        $status = $request->status;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;

        $transactions = Transaction::with(['deals', 'deals.item'])->wherePersonId($request->person_id);
        $transactions = $this->filterInvoiceBreakdownTransaction($transactions);
        $transactions = $transactions->orderBy('created_at', 'desc')->get();

        $ftransactions = Ftransaction::wherePersonId($request->person_id);
        $ftransactions = $this->filterInvoiceBreakdownFtransaction($ftransactions);
        $ftransactions = $ftransactions->orderBy('created_at', 'desc')->get();

        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->id);
            foreach($transaction->deals as $deal) {
                array_push($itemsId, $deal->item_id);
            }
        }
        foreach($ftransactions as $ftransaction) {
            array_push($ftransactionsId, $ftransaction->id);
        }
        $itemsId = array_unique($itemsId);
        $person_id = $request->person_id ? Person::find($request->person_id)->id : null ;

        if($request->export_excel) {
            $this->exportInvoiceBreakdownExcel($request, $ftransactionsId, $transactionsId, $itemsId, $person_id);
        }

        return view('freport.index', compact('request' ,'transactionsId', 'itemsId', 'person_id', 'ftransactionsId'));
    }

    // retrieve api data list for the person analog difference ()
    public function getFranchiseePeopleApi()
    {
        // showing total amount init
        $total_amount = 0;
        $input = request()->all();
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $transactions_analog = DB::raw("(SELECT MAX(transactions.analog_clock) AS latest_analog, DATE(transactions.delivery_date) AS analog_date, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                AND (status='Delivered' OR status='Verified Owe' OR status='Verified Paid')
                                GROUP BY people.id) transactions_analog");

        $ftransactions_analog = DB::raw("(SELECT MAX(ftransactions.analog_clock) AS latest_analog, DATE(ftransactions.collection_datetime) AS analog_date, people.id AS person_id
                                FROM ftransactions
                                LEFT JOIN people ON ftransactions.person_id=people.id
                                GROUP BY people.id) ftransactions_analog");

        $people = DB::table('people')
                        ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                        ->leftJoin($transactions_analog, 'people.id', '=', 'transactions_analog.person_id')
                        ->leftJoin($ftransactions_analog, 'people.id', '=', 'ftransactions_analog.person_id')
                        ->select(
                                    'people.id', 'people.cust_id', 'people.company', 'people.name', 'people.contact',
                                    'people.alt_contact', 'people.del_address', 'people.del_postcode', 'people.active',
                                    'people.payterm',
                                    'profiles.id AS profile_id', 'profiles.name AS profile_name',
                                    'transactions_analog.latest_analog AS stockin_analog',
                                    'ftransactions_analog.latest_analog AS collection_analog',
                                    DB::raw('(transactions_analog.latest_analog - ftransactions_analog.latest_analog) AS difference_analog'),
                                    'transactions_analog.analog_date AS stockin_date',
                                    'ftransactions_analog.analog_date AS collection_date'
                                );

        // reading whether search input is filled
        if(request('cust_id') or request('company')) {
            $people = $this->searchPeopleDBFilter($people);
        }else {
            if(request('sortName')) {
                $people = $people->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
            }
        }

        // add user profile filters
        $people = $this->filterUserDbProfile($people);

        // condition (exclude all H code)
        $people = $people->where('people.cust_id', 'NOT LIKE', 'H%');

        // add in franchisee checker
        if(auth()->user()->hasRole('franchisee')) {
            $people = $people->whereIn('people.franchisee_id', [auth()->user()->id]);
        }

        // showing is active only
        $people = $people->where('people.active', 'Yes');

        if($pageNum == 'All'){
            $people = $people->orderBy('people.cust_id', 'asc')->get();
        }else{
            $people = $people->orderBy('people.cust_id', 'asc')->paginate($pageNum);
        }

        $data = [
            'people' => $people,
        ];

        return $data;
    }

    // filter for transactions($query)
    private function filterInvoiceBreakdownTransaction($transactions)
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

    // filter for ftransactions($query)
    private function filterInvoiceBreakdownFtransaction($ftransactions)
    {
        $delivery_from = request('delivery_from');
        $delivery_to = request('delivery_to');

        if($delivery_from){
            $ftransactions = $ftransactions->whereDate('collection_datetime', '>=', $delivery_from);
        }
        if($delivery_to){
            $ftransactions = $ftransactions->whereDate('collection_datetime', '<=', $delivery_to);
        }

        return $ftransactions;
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
                $sheet->loadView('freport.invoicebreakdown_excel', compact('request', 'ftransactionsId', 'transactionsId', 'itemsId', 'person_id'));
            });
        })->download('xlsx');
    }

    // conditional filter parser(Collection $query)
    private function searchPeopleDBFilter($people)
    {
        $cust_id = request('cust_id');
        $company = request('company');

        if($cust_id){
            $people = $people->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($company){
            $people = $people->where('people.company', 'LIKE', '%'.$company.'%');
        }

        return $people;
    }
}
