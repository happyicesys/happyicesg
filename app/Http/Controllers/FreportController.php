<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Transaction;
use App\Ftransaction;
use App\Person;
use App\Variance;
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

        $transactions_analog = DB::raw("(SELECT MAX(transactions.analog_clock) AS latest_analog, MAX(DATE(transactions.delivery_date)) AS analog_date, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                AND (status='Delivered' OR status='Verified Owe' OR status='Verified Paid')
                                GROUP BY people.id) transactions_analog");

        $ftransactions_analog = DB::raw("(SELECT MAX(ftransactions.analog_clock) AS latest_analog, MAX(DATE(ftransactions.collection_datetime)) AS analog_date, people.id AS person_id
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

        if(auth()->user()->hasRole('subfranchisee')) {
            $people = $people->whereIn('people.franchisee_id', [auth()->user()->master_franchisee_id]);
        }

        // showing is active only
        $people = $people->where('people.active', 'Yes');

        if($pageNum == 'All'){
            $people = $people->orderBy('difference_analog', 'desc')->get();
        }else{
            $people = $people->orderBy('difference_analog', 'desc')->paginate($pageNum);
        }

        $data = [
            'people' => $people,
        ];

        return $data;
    }

    // adding variance entry()
    public function submitVarianceEntry()
    {
        $person_id = request('person_id');
        $datein = request('datein');
        $pieces = request('pieces');
        $reason = request('reason');

        $variances = Variance::create([
            'datein' => $datein,
            'pieces' => $pieces,
            'reason' => $reason,
            'person_id' => $person_id,
            'updated_by' => auth()->user()->id,
        ]);
    }

    // remove variance (int id)
    public function destroyVarianceApi($id)
    {
        $variance = Variance::findOrFail($id);
        $variance->delete();
    }

    // retrieve variances api()
    public function getVariancesIndex()
    {
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $variances = DB::table('variances')
                        ->leftJoin('people', 'variances.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('users AS update_person', 'update_person.id', '=', 'variances.updated_by')
                        ->select(
                                    'people.cust_id', 'people.company', 'people.name',
                                    DB::raw('DATE(variances.datein) AS datein'),
                                    'variances.pieces', 'variances.reason',
                                    'update_person.name AS updated_by'
                                );

        // reading whether search input is filled
        if(request('cust_id') or request('company') or request('datein_from') or request('datein_to')){
            $variances = $this->searchDBFilter($variances);
        }

        // add user profile filters
        $variances = $this->filterUserDbProfile($variances);

        // filter off franchisee
        if(auth()->user()->hasRole('franchisee')) {
            $variances = $variances->where('variances.franchisee_id', auth()->user()->id);
        }

        $totals = $this->calTotalPieces($variances);

        if(request('sortName')){
            $variances = $variances->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $variances = $variances->latest('variances.datein')->get();
        }else{
            $variances = $variances->latest('variances.datein')->paginate($pageNum);
        }

        $data = [
            'totals' => $totals,
            'variances' => $variances,
        ];

        if(request('export_excel')) {
            $this->exportFtransactionIndexExcel($data);
        }

        return $data;
    }

    // get invoice summary for invoice summary()
    public function getInvoiceSummaryApi()
    {
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;
        $collection_from = request('collection_from');
        $collection_to = request('collection_to');
        if ($collection_from and $collection_to) {
            $date_diff = Carbon::parse($collection_from)->diffInDays(Carbon::parse($collection_to)) + 1;
        } else {
            $date_diff = 1;
        }

        $first_date = DB::raw("(SELECT MIN(DATE(transactions.delivery_date)) AS delivery_date, people.id AS person_id FROM transactions
                                LEFT JOIN people ON people.id=transactions.person_id
                                GROUP BY people.id) AS first_date");
        $sales = DB::raw(
            "(SELECT SUM(ftransactions.sales) AS sales_qty,
                (SUM(ftransactions.sales)/ " . $date_diff . ") AS sales_avg_day,
                people.id AS person_id,
                ftransactions.id AS ftransaction_id
                FROM ftransactions
                LEFT JOIN people ON people.id=ftransactions.person_id
                WHERE DATE(ftransactions.collection_datetime)>='" . $collection_from . "'
                AND DATE(ftransactions.collection_datetime)<='" . $collection_to . "'
                GROUP BY people.id) AS sales"
        );
        $transactions_total = DB::raw(
            "(SELECT people.id AS person_id, 
                SUM(ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)) AS total 
                FROM transactions
                LEFT JOIN people ON people.id=transactions.person_id
                LEFT JOIN profiles ON profiles.id=people.profile_id
                WHERE DATE(transactions.delivery_date)>='" . $collection_from . "'
                AND DATE(transactions.delivery_date)<='" . $collection_to . "'
                AND (transactions.status = 'Delivered' OR transactions.status= 'Verified Paid' OR transactions.status= 'Verified Owe')
                GROUP BY people.id) AS transactions_total
            "
        );
        $transactions_owe = DB::raw(
            "(SELECT people.id AS person_id, 
                SUM(ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)) AS total 
                FROM transactions
                LEFT JOIN people ON people.id=transactions.person_id
                LEFT JOIN profiles ON profiles.id=people.profile_id
                WHERE DATE(transactions.delivery_date)>='" . $collection_from . "'
                AND DATE(transactions.delivery_date)<='" . $collection_to . "'
                AND (transactions.status = 'Delivered' OR transactions.status= 'Verified Owe')
                AND transactions.pay_status='Owe'
                GROUP BY people.id) AS transactions_owe
            "
        );
        $transactions_paid = DB::raw(
            "(SELECT people.id AS person_id, 
                SUM(ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)) AS total 
                FROM transactions
                LEFT JOIN people ON people.id=transactions.person_id
                LEFT JOIN profiles ON profiles.id=people.profile_id
                WHERE DATE(transactions.delivery_date)>='" . $collection_from . "'
                AND DATE(transactions.delivery_date)<='" . $collection_to . "'
                AND (transactions.status = 'Delivered' OR transactions.status= 'Verified Paid')
                AND transactions.pay_status='Paid'
                GROUP BY people.id) AS transactions_paid
            "
        );
        $transactions_stock = DB::raw(
            "(SELECT people.id AS person_id,
                ROUND(SUM(CASE WHEN deals.divisor>1 THEN (items.base_unit * deals.dividend/deals.divisor) ELSE (deals.qty * items.base_unit) END)) AS pieces
                FROM deals
                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                LEFT JOIN items ON items.id=deals.item_id
                LEFT JOIN people ON people.id=transactions.person_id
                LEFT JOIN profiles ON profiles.id=people.profile_id
                WHERE DATE(transactions.delivery_date)>='" . $collection_from . "'
                AND DATE(transactions.delivery_date)<='" . $collection_to . "'
                AND (transactions.status = 'Delivered' OR transactions.status= 'Verified Paid' OR transactions.status= 'Verified Owe')
                GROUP BY people.id) AS transactions_stock
            "
        );        

        $ftransactions = DB::table('ftransactions')
            ->leftJoin('people', 'people.id', '=', 'ftransactions.person_id')
            ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
            ->leftJoin('users AS franchisees', 'ftransactions.franchisee_id', '=', 'franchisees.id')
            ->leftJoin($first_date, 'people.id', '=', 'first_date.person_id')
            ->leftJoin($sales, 'people.id', '=', 'sales.person_id')
            ->leftjoin($transactions_total, 'people.id', '=', 'transactions_total.person_id')
            ->leftjoin($transactions_paid, 'people.id', '=', 'transactions_paid.person_id')
            ->leftjoin($transactions_owe, 'people.id', '=', 'transactions_owe.person_id')
            ->leftjoin($transactions_stock, 'people.id', '=', 'transactions_stock.person_id')
            ->select(
                'people.cust_id AS cust_id',
                'people.company AS company',
                'ftransactions.gst',
                'franchisees.name AS franchisee_name', 'franchisees.id AS franchisee_id',
                'first_date.delivery_date AS first_date',
                DB::raw('ROUND(SUM(ftransactions.total), 2) AS total'),
                DB::raw('ROUND(SUM(ftransactions.taxtotal), 2) AS taxtotal'),
                DB::raw('ROUND(SUM(ftransactions.total) - SUM(ftransactions.taxtotal), 2) AS finaltotal'),
                'transactions_total.total AS total_cost',
                DB::raw('ROUND(SUM(ftransactions.total) - transactions_total.total, 2) AS gross_profit'),
                DB::raw('ROUND((SUM(ftransactions.total) - transactions_total.total)/ SUM(ftransactions.total) * 100, 2) AS gross_profit_percent'),                
                'transactions_owe.total AS owe',
                'transactions_paid.total AS paid',
                'people.is_vending',
                'people.vending_monthly_rental',
                'people.vending_profit_sharing',
                'sales.sales_qty AS sales_qty',
                'sales.sales_avg_day AS sales_avg_day',
                'transactions_stock.pieces AS stock_in',
                DB::raw('ROUND(transactions_stock.pieces - sales.sales_qty, 2) AS delta')
            );

        if (request('collection_from') or request('collection_to') or request('cust_id') or request('company') or request('person_id') or request('franchisee_id') or request('is_active')) {
            $ftransactions = $this->searchInvoiceSummaryDBFilter($ftransactions);
        }

        // add user profile filters
        $ftransactions = $this->filterUserDbProfile($ftransactions);

        $ftransactions = $ftransactions->groupBy('people.id');

        if (request('sortName')) {
            $ftransactions = $ftransactions->orderBy(request('sortName'), $request->sortBy ? 'asc' : 'desc');
        } else {
            $ftransactions = $ftransactions->orderBy('cust_id');
        }

        // $fixedtotals = $this->calInvoiceSummaryFixedTotals($ftransactions);

        if ($pageNum == 'All') {
            $ftransactions = $ftransactions->get();
        } else {
            $ftransactions = $ftransactions->paginate($pageNum);
        }

        $dynamictotals = $this->calInvoiceSummaryDynamicTotals($ftransactions);

        $data = [
            'ftransactions' => $ftransactions,
            'dynamictotals' => $dynamictotals
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

    // pass value into filter search for DB (collection) [query]
    private function searchDBFilter($variances)
    {
        if(request('cust_id')){
            $variances = $variances->where('people.cust_id', 'LIKE', '%'.request('cust_id').'%');
        }
        if(request('company')){
            $com = request('company');
            $variances = $variances->where(function($query) use ($com){
                $query->where('people.company', 'LIKE', '%'.$com.'%')
                        ->orWhere(function ($query) use ($com){
                            $query->where('people.cust_id', 'LIKE', 'D%')
                                    ->where('people.name', 'LIKE', '%'.$com.'%');
                        });
                });
        }
        if(request('datein_from') === request('datein_to')){
            if(request('datein_from') != '' and request('datein_to') != ''){
                $variances = $variances->whereDate('variances.datein', '=', request('datein_to'));
            }
        }else{
            if(request('datein_from')){
                $variances = $variances->whereDate('variances.datein', '>=', request('datein_from'));
            }
            if(request('datein_to')){
                $variances = $variances->whereDate('variances.datein', '<=', request('datein_to'));
            }
        }
        if(request('sortName')){
            $variances = $variances->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $variances;
    }

    // calculate total pieces of ice cream for variances(Collection $variances)
    private function calTotalPieces($query)
    {
        $total_pieces = 0;
        $query1 = clone $query;
        $total_pieces = $query1->sum(DB::raw('ROUND(variances.pieces, 2)'));

        return $total_pieces;
    }

    // pass value into filter search for invoice summary (collection) [query]
    private function searchInvoiceSummaryDBFilter($ftransactions)
    {
        if (request('cust_id')) {
            $ftransactions = $ftransactions->where('people.cust_id', 'LIKE', '%' . request('cust_id') . '%');
        }
        if(request('is_active')) {
            $ftransactions = $ftransactions->where('people.active', request('is_active'));
        }
        if (request('company')) {
            $com = request('company');
            $ftransactions = $ftransactions->where(function ($query) use ($com) {
                $query->where('people.company', 'LIKE', '%' . $com . '%')
                    ->orWhere(function ($query) use ($com) {
                        $query->where('people.cust_id', 'LIKE', 'D%')
                            ->where('people.name', 'LIKE', '%' . $com . '%');
                    });
            });
        }
        if (request('collection_from') === request('collection_to')) {
            if (request('collection_from') != '' and request('collection_to') != '') {
                $ftransactions = $ftransactions->whereDate('ftransactions.collection_datetime', '=', request('collection_to'));
            }
        } else {
            if (request('collection_from')) {
                $ftransactions = $ftransactions->whereDate('ftransactions.collection_datetime', '>=', request('collection_from'));
            }
            if (request('collection_to')) {
                $ftransactions = $ftransactions->whereDate('ftransactions.collection_datetime', '<=', request('collection_to'));
            }
        }
        if (request('franchisee_id')) {
            $ftransactions = $ftransactions->where('ftransactions.franchisee_id', request('franchisee_id'));
        }
        if (request('person_id')) {
            $ftransactions = $ftransactions->where('ftransactions.person_id', request('person_id'));
        }
        if (request('sortName')) {
            $ftransactions = $ftransactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $ftransactions;
    }    

    // calculate totals for the invoice breakdown summary(collection $deals)
    private function calInvoiceSummaryFixedTotals($ftransactions)
    {
        $grand_total = 0;
        $taxtotal = 0;
        $subtotal = 0;
        $total_gross_money = 0;
        $total_gross_percent = 0;

        foreach ($deals->get() as $deal) {
            $grand_total += $deal->total;
            if ($deal->gst) {
                $taxtotal += $deal->gsttotal;
            }
            $subtotal += $deal->subtotal;
            $total_gross_money += $deal->gross_money;
            $total_gross_percent += $deal->gross_percent;
        }

        $totals = [
            'grand_total' => $grand_total,
            'taxtotal' => $taxtotal,
            'subtotal' => $subtotal,
            'total_gross_money' => $total_gross_money,
            'total_gross_percent' => $total_gross_percent
        ];

        return $totals;
    }

    // calculate dynamic average and totals for invoice breakdown summary(collection $deals)
    private function calInvoiceSummaryDynamicTotals($ftransactions)
    {
        $avg_grand_total = 0;
        $avg_finaltotal = 0;
        $avg_cost = 0;
        $avg_gross_profit = 0;
        $avg_gross_profit_percent = 0;
        $avg_sales_qty = 0;
        $avg_sales_avg_day = 0;
        $avg_stock_in = 0;
        $avg_delta = 0;

        $total_grand_total = 0;
        $total_taxtotal = 0;
        $total_finaltotal = 0;
        $total_cost = 0;
        $total_gross_profit = 0;
        $total_gross_profit_percent = 0;
        $total_owe = 0;
        $total_paid = 0;
        $total_sales_qty = 0;
        $total_stock_in = 0;
        $total_delta = 0;
        // placeholder
        $total_sales_avg_day = 0;

        $ftransactionscount = count($ftransactions);

        foreach ($ftransactions as $ftransaction) {
            $total_grand_total += $ftransaction->total;
            $total_finaltotal += $ftransaction->finaltotal;
            if ($ftransaction->gst) {
                $total_taxtotal += $ftransaction->taxtotal;
            }
            $total_cost += $ftransaction->total_cost;
            $total_gross_profit += $ftransaction->gross_profit;
            $total_gross_profit_percent += $ftransaction->gross_profit_percent;
            $total_owe += $ftransaction->owe;
            $total_paid += $ftransaction->paid;
            $total_sales_qty += $ftransaction->sales_qty;
            $total_delta += $ftransaction->delta;
            $total_stock_in += $ftransaction->stock_in;
            $total_sales_avg_day += $ftransaction->sales_avg_day;
        }

        if ($ftransactionscount > 0) {
            $avg_grand_total = $total_grand_total / $ftransactionscount;
            $avg_finaltotal = $total_finaltotal / $ftransactionscount;
            $avg_cost = $total_cost / $ftransactionscount;
            $avg_gross_profit = $total_gross_profit / $ftransactionscount;
            $avg_gross_profit_percent = $total_gross_profit_percent / $ftransactionscount;
            $avg_sales_qty = $total_sales_qty / $ftransactionscount;
            $avg_sales_avg_day = $total_sales_avg_day / $ftransactionscount;
            $avg_delta = $total_delta / $ftransactionscount;
            $avg_stock_in = $total_stock_in / $ftransactionscount;
        }

        $totals = [
            'avg_grand_total' => $avg_grand_total,
            'avg_finaltotal' => $avg_finaltotal,
            'avg_cost' => $avg_cost,
            'avg_gross_profit' => $avg_gross_profit,
            'avg_gross_profit_percent' => $avg_gross_profit_percent,
            'avg_sales_qty' => $avg_sales_qty,
            'avg_sales_avg_day' => $avg_sales_avg_day,
            'avg_delta' => $avg_delta,
            'avg_stock_in' => $avg_stock_in,

            'total_grand_total' => $total_grand_total,
            'total_finaltotal' => $total_finaltotal,
            'total_taxtotal' => $total_taxtotal,
            'total_cost' => $total_cost,
            'total_gross_profit' => $total_gross_profit,
            'total_gross_profit_percent' => $total_gross_profit_percent,
            'total_owe' => $total_owe,
            'total_paid' => $total_paid,
            'total_sales_qty' => $total_sales_qty,
            'total_delta' => $total_delta,
            'total_stock_in' => $total_stock_in
        ];

        return $totals;
    }    
}
