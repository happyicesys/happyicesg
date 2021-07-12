<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

use Venturecraft\Revisionable\Revision;
use Response;
use App;
use DB;
use Auth;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Unitcost;
use App\Item;
use App\Person;
use App\Price;
use App\Deal;
use Carbon\Carbon;
use App\Profile;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Laracasts\Flash\Flash;
use App\EmailAlert;
use App\DtdPrice;
use App\DtdTransaction;
use App\DtdDeal;
use App\GeneralSetting;
use App\ImportTransactionExcel;
use App\Invattachment;
use App\Operationdate;
use App\TransSubscription;
use App\User;
use App\Deliveryorder;
use App\Transactionpersonasset;
use App\Services\DealService;
use Illuminate\Support\Facades\Storage;
// use App\Ftransaction;

// traits
use App\HasProfileAccess;
use App\CreateRemoveDealLogic;
use App\GetIncrement;
use App\HasFranchiseeAccess;
use App\Traits\HasCustcategoryAccess;

class TransactionController extends Controller
{
    use HasProfileAccess, CreateRemoveDealLogic, HasFranchiseeAccess, HasCustcategoryAccess;
    //qty status condition
    /*
        qty_status = 1 (Stock Order/ Confirmed)
        qty_status = 2 (Actual Stock Deducted/ Delivered)
        qty_status = 3 (Stock Removed/ Deleted || Cancelled)
    */
    private $dealService;


    //auth-only login can see
    public function __construct(DealService $dealService)
    {
        $this->middleware('auth');
        $this->dealService = $dealService;
    }

    // get transactions api data based on delivery date
    public function getData(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 200;
        // dd($this->dealService->getDeals($request));

        $transactions = $this->getTransactionsData();


        $total_amount = $this->calArrTransactionTotal($transactions);
        $delivery_total = $this->calDBDeliveryTotal($transactions);

        if(request('sortName')){
            $transactions = $transactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $transactions = $transactions->latest('transactions.created_at')->get();
        }else{
            $transactions = $transactions->latest('transactions.created_at')->paginate($pageNum);
        }

        if($transactions) {
            foreach($transactions as $transaction) {
                $transaction->deals = $this->getTransactionDeals($transaction->id);
            }
        }

        $data = [
            'total_amount' => $total_amount + $delivery_total,
            'transactions' => $transactions,
        ];
        return $data;
/*
        $transactionsArr = $this->dealService->getTransactions($request, $this->getPerPage, true);

        $data = [
            'total_amount' => $transactionsArr['total'],
            'transactions' => $transactionsArr['transactions']
        ];
        return $data; */
    }

    // return job assign data api
    public function getJobAssignData()
    {
        $transactions = $this->getTransactionsData();

        $driverarr = clone $transactions;

        $driverarr = $driverarr->distinct('transactions.driver')->orderBy('transactions.driver')->select('transactions.driver')->get();
        $transactionarr = $transactions;
        if(request('transactions_row')) {
            $transaction_rows = implode(',', array_map('trim',explode("\n", request('transactions_row'))));
            $transactionarr = $transactionarr->orderByRaw('FIELD(transactions.id, '.$transaction_rows.')');
        }
        $transactionarr = $transactionarr->orderBy('transactions.sequence')->orderBy('transactions.id', 'desc')->get();

        if($transactionarr) {
            foreach($transactionarr as $transaction) {
                $transaction->deals = $this->getTransactionDeals($transaction->id);
            }
        }

        $collections = [];
        $grand_total = 0;
        $grand_qty = 0;
        $grand_count = 0;
        $grand_delivered_total = 0;
        $grand_delivered_qty = 0;
        $grand_delivered_count = 0;
        foreach($driverarr as $index => $driver) {
            // dd($driverarr, $transactionarr);
            $drivertable = [
                'name' => ($driver->driver == '' or $driver->driver == null) ? 'Unassigned' : $driver->driver
            ];
            $total_amount = 0;
            $total_qty = 0;
            $total_count = 0;
            $delivered_amount = 0;
            $delivered_qty = 0;
            $delivered_count = 0;
            foreach($transactionarr as $key => $transaction) {
                if($transaction->driver == $driver->driver) {
                    $total_amount += $transaction->total;
                    $transaction->label_color = '';
                    $transaction->back_color = '';
                    $status = $transaction->status;
                    switch($status) {
                        case 'Cancelled' :
                            $transaction->label_color = 'white';
                            $transaction->back_color = 'red';
                            break;
                        case 'Pending':
                            $transaction->label_color = 'red';
                            $transaction->back_color = '';
                            break;
                        case 'Confirmed':
                            $transaction->back_color = '';
                            $transaction->label_color = '';
                            if($transaction->total_qty == 0) {
                                $transaction->label_color = 'red';
                            }
                            break;
                        case 'Delivered':
                        case 'Verified Owe':
                        case 'Verified Paid':
                            $transaction->label_color = 'white';
                            $transaction->back_color = 'green';
                            break;
                    }
                    if($transaction->status == 'Cancelled' or $transaction->status == 'Delivered' or $transaction->status == 'Verified Owe' or $transaction->status == 'Verified Paid') {
                        $delivered_amount += $transaction->total;
                        $delivered_qty += $transaction->total_qty;
                        $delivered_count += 1;
                        $grand_delivered_total += $transaction->total;
                        $grand_delivered_qty += $transaction->total_qty;
                        $grand_delivered_count += 1;
                    }
                    $grand_total += $transaction->total;
                    $total_qty += $transaction->total_qty;
                    $grand_qty += $transaction->total_qty;
                    $total_count += 1;
                    $grand_count += 1;
                    $drivertable['transactions'][$key] = $transaction;
                    unset($transactionarr[$key]);
                }
            }
            $drivertable['total_amount'] = number_format($total_amount, 2);
            $drivertable['total_qty'] = number_format($total_qty, 2);
            $drivertable['total_count'] = $total_count;
            $drivertable['delivered_amount'] = number_format($delivered_amount, 2);
            $drivertable['delivered_qty'] = number_format($delivered_qty, 2);
            $drivertable['delivered_count'] = $delivered_count;
            // array_push($collections['drivers'], $drivertable);
            $collections['drivers'][$index] = $drivertable;
        }
        $collections['grand_total'] = number_format($grand_total, 2);
        $collections['grand_qty'] = number_format($grand_qty, 2);
        $collections['grand_count'] = $grand_count;
        $collections['grand_delivered_total'] = number_format($grand_delivered_total, 2);
        $collections['grand_delivered_qty'] = number_format($grand_delivered_qty, 2);
        $collections['grand_delivered_count'] = $grand_delivered_count;

        return $collections;
    }

    public function getJobAssignPdf(Request $request)
    {
        // dd($request->all());
        $now = Carbon::now()->format('d-m-Y H:i');
        $data = [];
        $data = $this->getJobAssignData();
        $data['request'] = $request->all();
        $filename = 'JobAssign_';
        if($request->driver) {
            $filename .= $request->driver.'_';
        }else {
            $filename .= 'All_';
        }
        $filename .= Carbon::today()->format('Ymd').'.pdf';
        $pdf = PDF::loadView('transaction.jobassign_pdf', $data);
        $pdf->setPaper('a4');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 8000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);

        return $pdf->download($filename);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('transaction.index');
    }

    public function hdprofileIndex()
    {
        return view('hdprofile.index');
    }

    public function jobAssignIndex()
    {
        return view('transaction.jobassign');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transaction.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'person_id' => 'required',
        ],[
            'person_id.required' => 'Please choose a customer',
        ]);

        $request->merge(array('updated_by' => Auth::user()->name));
        $request->merge(['order_date' => Carbon::today()]);
        $request->merge(['delivery_date' => Carbon::today()]);
        $request->merge(['created_by' => auth()->user()->id]);
        $request->merge(['merchandiser' => auth()->user()->id]);

        // haagen daz user logic, open delivery order
/*
        if(auth()->user()->hasRole('hd_user')) {
            $request->merge(array('is_deliveryorder' => 1));
        } */


        $person = Person::findOrFail(request('person_id'));
        $request->merge(['del_postcode' => $person->del_postcode]);
        $request->merge(['bill_postcode' => $person->bill_postcode]);
        $request->merge(['del_lat' => $person->del_lat]);
        $request->merge(['del_lng' => $person->del_lng]);

        // temporary hard code to set haagen daz as DO
        if($person->cust_id == 'B301') {
            $request->merge(array('is_deliveryorder' => 1));
        }

        $request->merge(['status' => 'Pending']);
        // filter delivery date if the invoice lock date is before request delivery date
        if($freeze_date = GeneralSetting::firstOrFail()->INVOICE_FREEZE_DATE) {
            if($freeze_date->min(Carbon::parse($request->delivery_date)) != $freeze_date) {
                Flash::error('The delivery date is locked, alter the invoice lock date to after '.Carbon::parse($freeze_date)->format('Y-m-d'));
                return back();
            }
        }

        if($request->po_no) {
            $request->merge(array('po_no' => trim($request->po_no)));

            if($transaction->person->cust_id[0] === 'P'){
                $this->validate($request, [
                    'po_no' => 'unique:transactions,po_no,'.$transaction->id
                ]);
            }
        }

        if($request->input('discard')) {
            $request->merge(['is_discard' => 1]);
        }

        $input = $request->all();
        $transaction = Transaction::create($input);
        // create dtd transaction once detect person code is D
        if($transaction->person->cust_id[0] === 'D'){
            $request->merge(array('type' => 'Deal'));
            $dtdtransaction = DtdTransaction::create($input);
            $dtdtransaction->transaction_id = $transaction->id;
            $dtdtransaction->save();
            $transaction->dtdtransaction_id = $dtdtransaction->id;
            $transaction->save();
        }

        // check profile is vending then analog required
        if($transaction->person->is_vending) {
            $transaction->is_required_analog = 1;
            $transaction->save();
        }

        if($transaction->del_postcode == $transaction->person->del_postcode) {
            $transaction->del_lat = $transaction->person->del_lat;
            $transaction->del_lng = $transaction->person->del_lng;
            $transaction->save();
        }else {
            if(($transaction->del_lat == $transaction->person->del_lat) or ($transaction->del_lng == $transaction->person->del_lat)) {
                $transaction->del_lat = null;
                $transaction->del_lng = null;
                $transaction->save();
            }
        }

        // create delivery order if is delivery order
        if($transaction->is_deliveryorder) {
            $do = new Deliveryorder();
            $do->transaction_id = $transaction->id;
            $do->requester = auth()->user()->id;
            $do->pickup_date = Carbon::today()->addDay()->toDateString();
            $do->delivery_date1 = Carbon::today()->addDay()->toDateString();
            $do->save();
        }

        // auto becomes assigned driver if have driver role
        if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
            $transaction->driver = auth()->user()->name;
            $transaction->save();
        }

        // operation worksheet management
        $this->operationDatesSync($transaction->id);

        return Redirect::action('TransactionController@edit', $transaction->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        return $transaction;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);

        $invattachments = $transaction->invattachments;

        $person = Person::findOrFail($transaction->person_id);

        // retrieve manually to order product id asc
        if($transaction->person->cust_id[0] === 'D'){
            $prices = DB::table('dtdprices')
                        ->leftJoin('items', 'dtdprices.item_id', '=', 'items.id')
                        ->select(
                            'dtdprices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id', 'items.base_unit as pieces', 'items.is_inventory')
                        ->where('items.is_active', 1)
                        ->orderBy('product_id')
                        ->get();
        }else if($transaction->person->cust_id[0] === 'H'){
            $prices = DB::table('d2d_online_sales')
                            ->leftJoin('people', 'd2d_online_sales.person_id', '=', 'people.id')
                            ->leftJoin('items', 'd2d_online_sales.item_id', '=', 'items.id')
                            ->leftJoin('prices', function($join) {
                                $join->on('prices.person_id', '=', 'people.id')
                                        ->on('prices.item_id', '=', 'items.id');
                            })
                            ->select(
                                'prices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id', 'items.base_unit as pieces', 'items.is_inventory'
                                )
                            ->where('items.is_active', 1)
                            ->orderBy('product_id')
                            ->get();
        }else{
            $prices = DB::table('prices')
                        ->leftJoin('items', 'prices.item_id', '=', 'items.id')
                        ->select(
                            'prices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id', 'items.base_unit as pieces', 'items.is_active', 'items.is_inventory'
                        )
                        ->where('prices.person_id', '=', $transaction->person_id)
                        ->where('items.is_active', 1)
                        ->orderBy('product_id')
                        ->get();
/*            $prices = Price::with('item')
                            ->where('person_id', $transaction->person_id)
                            ->orderBy('product_id')
                            ->get();*/
        }

        return view('transaction.edit', compact('transaction', 'person', 'prices', 'invattachments'));
    }

    // return transaction related components, deals (int transaction_id)
    public function editApi($transaction_id)
    {
        $total = 0;
        $subtotal = 0;
        $tax = 0;

        $transaction = Transaction::with(['person', 'deliveryorder'])->findOrFail($transaction_id);

        $deals = DB::table('deals')
                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                    ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                    ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                    ->select(
                                'deals.transaction_id', 'deals.dividend', 'deals.divisor', 'deals.qty', 'deals.qty_before', 'deals.qty_after', 'deals.unit_price', 'deals.amount', 'deals.id AS deal_id',
                                'items.id AS item_id', 'items.product_id', 'items.name AS item_name', 'items.remark AS item_remark', 'items.is_inventory', 'items.unit',
                                'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                'transactions.del_postcode', 'transactions.status', 'transactions.delivery_date', 'transactions.driver',
                                DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                                                    CASE
                                                    WHEN transactions.is_gst_inclusive=0
                                                    THEN total*((100+transactions.gst_rate)/100)
                                                    ELSE transactions.total
                                                    END)
                                                ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2) AS total'),
                                DB::raw('(CASE WHEN items.is_inventory = 1 THEN deals.qty ELSE 0 END) AS qty'),
                                'transactions.pay_status','transactions.updated_by', 'transactions.updated_at', 'transactions.delivery_fee', 'transactions.id',
                                'profiles.id as profile_id', 'transactions.gst', 'transactions.is_gst_inclusive', 'transactions.gst_rate', 'transactions.is_important', 'transactions.is_discard',
                                DB::raw('
                                    ROUND(CASE WHEN deals.divisor > 1
                                    THEN (items.base_unit * deals.dividend/deals.divisor)
                                    ELSE (items.base_unit * deals.qty)
                                    END, 0) AS pieces
                                ')
                            )
                    ->where('deals.transaction_id', $transaction->id)
                    ->orderBy('items.is_inventory', 'desc')
                    ->orderBy('items.product_id', 'asc')
                    ->get();

        $subtotal = 0;
        $tax = 0;
        $total = $transaction->total;
        $total_qty = 0;

        foreach($deals as $deal) {
            $total_qty += $deal->qty;
        }

        if($transaction->gst) {
            if($transaction->is_gst_inclusive) {
                $total = $transaction->total;
                $tax = $transaction->total - $transaction->total/((100 + $transaction->gst_rate)/ 100);
                $subtotal = $transaction->total - $tax;
            }else {
                $subtotal = $transaction->total;
                $tax = $transaction->total * ($transaction->gst_rate)/100;
                $total = $transaction->total + $tax;
            }
        }

        $subtotal = number_format($subtotal, 2);
        $tax = number_format($tax, 2);
        $total = number_format($total, 2);
        // dd($subtotal, $tax, $total);

        $delivery_fee = $transaction->delivery_fee;

        if($delivery_fee) {
            $total += number_format($delivery_fee, 2);
        }

        // die(var_dump($transaction));
        return $data = [
            'transaction' => $transaction,
            'deals' => $deals,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'total_qty' => $total_qty,
            'delivery_fee' => $delivery_fee
        ];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionRequest $request, $id)
    {

        // dd(request()->all());
        // dynamic form arrays
        $quantities = $request->qty;
        $amounts = $request->amount;
        $quotes = $request->quote;
        $transaction = Transaction::findOrFail($id);
        // find out deals created
        $deals = Deal::where('transaction_id', $transaction->id)->get();

        $delivery_date = $transaction->delivery_date;
        $previous_delivery_date = $transaction->delivery_date;
        if($transaction->delivery_date != $request->delivery_date) {
            $delivery_date = $request->delivery_date;
        }

        if($request->input('save')){
            $request->merge(array('status' => 'Pending'));
        }else if($request->input('del_paid')){
            $this->vendingMachineValidation($request, $id);
            $request->merge(array('status' => 'Delivered'));
            $request->merge(array('pay_status' => 'Paid'));
            if(! $request->paid_by){
                $request->merge(array('paid_by' => Auth::user()->name));
            }
            $request->merge(array('paid_at' => Carbon::now()->format('Y-m-d h:i A')));

            if(!$request->driver){
                $request->merge(array('driver'=>Auth::user()->name));
            }

            if($transaction->is_deliveryorder) {
                $this->updateIsWarehouseTransactionpersonassets($transaction->id);
            }

            if(count($deals) == 0){
                Flash::error('Please entry the list');
                return Redirect::action('TransactionController@edit', $transaction->id);
            }
        }elseif($request->input('del_owe')){
            $this->vendingMachineValidation($request, $id);
            $request->merge(array('status' => 'Delivered'));
            $request->merge(array('pay_status' => 'Owe'));
            if(! $request->driver){
                $request->merge(array('driver'=>Auth::user()->name));
            }
            $request->merge(array('paid_by'=>null));

            if($transaction->is_deliveryorder) {
                $this->updateIsWarehouseTransactionpersonassets($transaction->id);
            }

            if(count($deals) == 0){
                Flash::error('Please entry the list');
                return Redirect::action('TransactionController@edit', $transaction->id);
            }
        }elseif($request->input('del')) {
            $this->vendingMachineValidation($request, $id);
            $request->merge(array('status' => 'Delivered'));

            if(! $request->driver){
                $request->merge(array('driver'=>Auth::user()->name));
            }

            if($transaction->is_deliveryorder) {
                $this->updateIsWarehouseTransactionpersonassets($transaction->id);
            }

            if(count($deals) == 0){
                Flash::error('Please entry the list');
                return Redirect::action('TransactionController@edit', $transaction->id);
            }
        }elseif($request->input('paid')){
            $request->merge(array('pay_status' => 'Paid'));

            if(! $request->paid_by){
                $request->merge(array('paid_by' => Auth::user()->name));
            }
            if($transaction->driver == null or $transaction->driver == '') {
                $request->merge(array('driver' => null));
            }
            $request->merge(array('paid_at' => Carbon::now()->format('Y-m-d h:i A')));

            if(count($deals) == 0){
                Flash::error('Please entry the list');
                return Redirect::action('TransactionController@edit', $transaction->id);
            }

        }elseif($request->input('confirm')){
            // confirmation must with the entries start
/*
            if(!$transaction->is_deliveryorder) {
                if($quantities and $amounts) {
                    $request->merge(array('status' => 'Confirmed'));
                }else{
                    Flash::error('The list cannot be empty upon confirmation');
                    return Redirect::action('TransactionController@edit', $transaction->id);
                } */
            if($transaction->is_deliveryorder){
                $this->saveDoByTransactionid($transaction->id);
                $this->validate($request, [
                    'job_type' => 'required',
                    'po_no' => 'required',
                    'pickup_date' => 'required',
                    'pickup_attn' => 'required',
                    'pickup_contact' => 'required',
                    'pickup_postcode' => 'required',
                    'pickup_location_name' => 'required',
                    'pickup_address' => 'required',
                    'delivery_attn' => 'required',
                    'delivery_contact' => 'required',
                    'delivery_postcode' => 'required',
                    'delivery_location_name' => 'required',
                    'delivery_address' => 'required',
                    'requester_name' => 'required',
                    'requester_contact' => 'required'
                ], [
                    'job_type.required' => 'Please select the Job Type',
                    'po_no.required' => 'Please select the PO Number',
                    'pickup_date.required' => 'Please choose the Pickup Date',
                    'pickup_attn.required' => 'Please fill in the Pickup Contact Person',
                    'pickup_contact.required' => 'Please fill in the Pickup Tel No.',
                    'pickup_postcode.required' => 'Please fill in the Pickup Postcode',
                    'pickup_location_name.required' => 'Please fill in the Pickup Location Name',
                    'pickup_address.required' => 'Please fill in the Pickup Address',
                    'delivery_attn.required' => 'Please fill in the Delivery Contact Person',
                    'delivery_contact.required' => 'Please fill in the Delivery Tel No.',
                    'delivery_postcode.required' => 'Please fill in the Delivery Postcode',
                    'delivery_location_name.required' => 'Please fill in the Delivery Location Name',
                    'delivery_address.required' => 'Please fill in the Delivery Address',
                    'requester_name.required' => 'Please fill in the Requester Name',
                    'requester_contact.required' => 'Please fill in the Requester Contact'
                ]);

                $do = $transaction->deliveryorder;
                $request->merge(array('status' => 'Confirmed'));
                $do->submission_datetime = Carbon::now();
                $do->save();

                $transaction->delivery_date = $request->pickup_date ?: Carbon::now();
                $transaction->save();

                $this->sendDoConfirmEmailAlert($transaction->id);
            }else {
                $request->merge(array('status' => 'Confirmed'));
            }

        }elseif($request->input('unpaid')){
            $request->merge(array('pay_status' => 'Owe'));
            $request->merge(array('paid_by' => null));
            $request->merge(array('paid_at' => null));
        }elseif($request->input('update')){
            if($transaction->status === 'Confirmed'){
                // $request->merge(array('driver' => null));
                if($transaction->pay_status == 'Owe') {
                    $request->merge(array('paid_by' => null));
                    $request->merge(array('paid_at' => null));
                }
            }else if(($transaction->status === 'Delivered' or $transaction->status === 'Verified Owe') and $transaction->pay_status === 'Owe'){
                $this->vendingMachineValidation($request, $id);
                if($transaction->pay_status == 'Owe') {
                    $request->merge(array('paid_by' => null));
                    $request->merge(array('paid_at' => null));
                }
            }else {
                $this->vendingMachineValidation($request, $id);
            }
        }

        // validate unique prefix for P
        // dd($request->all(), $transaction->person->cust_id[0]);
        if($request->po_no) {
            $request->merge(array('po_no' => trim($request->po_no)));

            if($transaction->person->cust_id[0] === 'P'){
                $this->validate($request, [
                    'po_no' => 'unique:transactions,po_no,'.$transaction->id
                ]);
            }
        }

        // online custcategory groups must have attn name and contact
        // dd($transaction->person->id, $transaction->person->custcategory->name, $transaction->person->custcategory->custcategoryGroup->name);
        if($transaction->person->custcategory->custcategoryGroup->name === 'ONLINE') {

            $this->validate($request, [
                'name' => 'required',
                'contact' => 'required'
            ]);
        }

        // filter delivery date if the invoice lock date is before request delivery date
        if($freeze_date = GeneralSetting::firstOrFail()->INVOICE_FREEZE_DATE) {
            if($freeze_date->min(Carbon::parse($request->delivery_date)) != $freeze_date) {
                Flash::error('The delivery date is locked, please choose the date after '.Carbon::parse($freeze_date)->format('Y-m-d'));
                return back();
            }
        }

        $analog_clock = request('analog_clock');

        if($transaction->person->is_vending and $transaction->is_required_analog) {
            $vendcash_check = Transaction::whereHas('deals', function($q) {
                                    $q->whereHas('item', function($q) {
                                        $q->where('product_id', '051');
                                    });
                                })
                                ->where('id', $transaction->id)
                                ->get();

            if(count($vendcash_check) > 0) {
                if($analog_clock > 0) {
                    $delivery_date = $transaction->delivery_date ? $transaction->delivery_date : Carbon::today()->toDateString();

                    $prev_inv = Transaction::where('person_id', $transaction->person_id)->where('is_required_analog', 1)->whereNotIn('id', [$transaction->id])->whereDate('delivery_date', '<', $delivery_date)->latest()->first();

                    if($prev_inv) {
                        $prev_analog = (int)Transaction::where('person_id', $transaction->person_id)->where('is_required_analog', 1)->whereNotIn('id', [$transaction->id])->whereDate('delivery_date', '<', $delivery_date)->latest()->first()->analog_clock;


                        $current_analog = (int)request('analog_clock');

                        if($current_analog < $prev_analog) {
                            Flash::error('Analog Clock value must be equals or greater than previous invoice ('.$prev_analog.')');
                            return redirect()->action('TransactionController@edit', $transaction->id);
                        }
                    }else {
                        Flash::error('Vend cash shouldnt be received when the previous invoice is not detected');
                        return redirect()->action('TransactionController@edit', $transaction->id);
                    }
                }else {
                    if($analog_clock == 0 or $analog_clock == null) {
                        Flash::error('Analog Clock must be filled and cannot be 0');
                        return redirect()->action('TransactionController@edit', $transaction->id);
                    }
                }
            }
        }

        // analog required validate by roles
        if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('operation')) {
            $request->merge(array('is_required_analog' => $request->has('is_required_analog') ? 1 : 0));
        }else {
            $request->merge(array('is_required_analog' => $transaction->is_required_analog));
        }

        $request->merge(array('person_id' => $request->input('person_copyid')));
        $request->merge(array('updated_by' => Auth::user()->name));
        $request->merge(array('gst' => $transaction->person->profile->gst));
        $request->merge(array('is_gst_inclusive' => $transaction->person->is_gst_inclusive));
        $request->merge(array('gst_rate' => $transaction->person->gst_rate));
        $transaction->update($request->all());

        // operation worksheet management
        $this->operationDatesSync($transaction->id, $delivery_date, $previous_delivery_date);

        $dealArr = [];
        // dd($quantities, $amounts);
        if($quantities or $amounts) {
            // dd($quantities, $quotes, $amounts);
            if((array_filter($quantities) != null and array_filter($amounts) != null) or ($transaction->is_discard and array_filter($quantities) != null)) {
                $evenStockAmount = 0;
                foreach($quantities as $index => $qty) {
                    if($transaction->is_discard) {
                        $price = Price::where('person_id', $transaction->person_id)->where('item_id', $index)->first();
                        if($qty) {
                            array_push($dealArr, [
                                'item_id' => $index,
                                'qty' => $qty,
                                'quote' => $price->quote_price,
                                'amount' => -($price->quote_price * $qty),
                            ]);
                        }

                        if($transaction->person->is_vending or $transaction->person->is_dvm or $transaction->person->is_combi) {
                            $evenStockAmount += $price->quote_price * $qty;
                        }
                    }else {
                        if($qty) {
                            array_push($dealArr, [
                                'item_id' => $index,
                                'qty' => $qty,
                                'quote' => $quotes[$index],
                                'amount' => $amounts[$index]
                            ]);
                        }
                    }
                }
                if($evenStockAmount > 0) {
                    $item = Item::where('product_id', '051a')->first();

                    array_push($dealArr, [
                        'item_id' => $item->id,
                        'qty' => 1,
                        'quote' => $evenStockAmount,
                        'amount' => $evenStockAmount
                    ]);
                }
            }
        }
        // dd($dealArr, $quantities);
        // sync deals
        if($transaction->status === 'Confirmed'){
            // dd($quantities, $amounts, $quotes);
            // $this->syncDeal($transaction, $quantities, $amounts, $quotes, 1);
            $this->syncDeal($transaction, $dealArr, 1);
        }else if($transaction->status === 'Delivered' or $transaction->status === 'Verified Owe' or $transaction->status === 'Verified Paid'){
            // $this->syncDeal($transaction, $quantities, $amounts, $quotes, 2);
            $this->syncDeal($transaction, $dealArr, 2);
        }

        if($transaction->person->cust_id[0] === 'D'){
            $this->syncOrder($transaction->id);
            // sync transaction status once not belongs to those status
            if($transaction->status !== 'Pending' or $transaction->status !== 'Verify Owe' or $transaction->status !== 'Verify Paid'){
                $dtdtransaction = DtdTransaction::findOrFail($transaction->dtdtransaction_id);
                // sync to be replace <-> original
                $this->transactionXChange($dtdtransaction, $transaction);
            }
        }

        // waive off delivery fees if update with more than 4 quantities (dividend)
        if($transaction->person->cust_id[0] === 'H') {
            $total_qty = Deal::whereTransactionId($transaction->id)->sum('dividend');
            if($total_qty >= 4) {
                $transaction->delivery_fee = 0;
                $transaction->save();
            }
        }
        // given this is a delivery order
        if($transaction->is_deliveryorder) {
            $transaction->del_postcode = $request->delivery_postcode;
            $transaction->save();
            $this->saveDoByTransactionid($transaction->id);
        }

        if($transaction->del_postcode == $transaction->person->del_postcode) {
            $transaction->del_lat = $transaction->person->del_lat;
            $transaction->del_lng = $transaction->person->del_lng;
            $transaction->save();
        }else {
            if(($transaction->del_lat == $transaction->person->del_lat) or ($transaction->del_lng == $transaction->person->del_lat)) {
                $transaction->del_lat = null;
                $transaction->del_lng = null;
                $transaction->save();
            }
        }

        return Redirect::action('TransactionController@edit', $transaction->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if($request->input('form_delete')){

            $transaction = Transaction::findOrFail($id);
            $transaction->cancel_trace = $transaction->status;
            $transaction->status = 'Cancelled';
            $transaction->updated_by = auth()->user()->name;
            $transaction->save();

        // operation worksheet management
            $this->operationDatesSync($transaction->id);

            if($transaction->dtdtransaction_id){
                $dtdtransaction = DtdTransaction::findOrFail($transaction->dtdtransaction_id);
                $dtdtransaction->cancel_trace = $dtdtransaction->status;
                $dtdtransaction->status = 'Cancelled';
                $dtdtransaction->updated_by = auth()->user()->name;
                $dtdtransaction->save();
            }

            $this->dealDeleteMultiple($transaction->id);

            // $this->doAssetsDeleteMultiple($transaction->id);

            return Redirect::action('TransactionController@edit', $transaction->id);

        }else if($request->input('form_wipe')){
            $transaction = Transaction::findOrFail($id);
            $this->removeOperationDates($transaction->id);
            if($transaction->dtdtransaction_id){
                $dtdtransaction = DtdTransaction::find($transaction->dtdtransaction_id);
                if($dtdtransaction){
                    $dtdtransaction->delete();
                }
            }
            $transaction->delete();

            return Redirect::action('PersonController@edit', $transaction->person->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *transaction
     * @param  int  $id
     * @return json
     */
    public function destroyAjax($id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->delete();

        return $transaction->id . 'has been successfully deleted';
    }

    public function getCust($person_id)
    {
        $person =  Person::findOrFail($person_id);

        return $person;
    }

    public function getItem($person_id)
    {
/*        return Item::whereHas('prices', function($query) use ($person_id){

            $query->where('person_id', $person_id)->whereNotNull('quote_price');

        })->get();*/
        //select(DB::raw("CONCAT(product_id,' - ',name,' - ',remark) AS full, id"))->lists('full', 'id');
        $item =  Item::with(['prices' => function($query) use ($person_id){

            $query->where('person_id', $person_id);

        }])->get();

        return $item;


    }

    // return the price lists set in the person
    public function getPrice($person_id, $item_id)
    {

        return Price::with('item')->where('person_id', $person_id)->where('item_id', $item_id)->first();

    }

    public function storeCust($trans_id, Request $request)
    {
        $input = $request->all();

        $transaction = Transaction::findOrFail($trans_id);

        //take the first value of the array
        $transaction->person_id = reset($input);

        $transaction->save();

        return "Sucess updating transaction #" . $transaction->id;

    }

    public function storeCustcode($trans_id, Request $request)
    {

        $transaction = Transaction::findOrFail($trans_id);

        //take the first value of the array
        $transaction->person_code = $request->input('person_code');

        $transaction->save();

        return "Sucess updating transaction #" . $transaction->id;

    }

    public function storeTotal($trans_id, Request $request)
    {
        $input = $request->all();

        $transaction = Transaction::findOrFail($trans_id);

        //take the first value of the array
        $transaction->total = reset($input);

        $transaction->save();

        return "Sucess updating transaction #" . $transaction->id;

    }

    public function storeTotalQty($trans_id, Request $request)
    {
        $input = $request->all();

        $transaction = Transaction::findOrFail($trans_id);

        //take the first value of the array
        $transaction->total_qty = reset($input);

        $transaction->save();

        return "Sucess updating transaction #" . $transaction->id;

    }

    // generate pdf invoice for transaction
    public function generateInvoice($id)
    {
        $type = 'invoice';

        if(request()->has('value')) {
            $type = 'do';
        }
        $transaction = Transaction::findOrFail($id);
        $person = Person::findOrFail($transaction->person_id);
        $deals = Deal::whereTransactionId($transaction->id)->get();
        $totalprice = DB::table('deals')->whereTransactionId($transaction->id)->sum('amount');
        $totalqty = DB::table('deals')->leftJoin('items', 'items.id', '=', 'deals.item_id')->where('items.is_inventory', 1)->whereTransactionId($transaction->id)->sum('qty');
        $transactionpersonassets = DB::table('transactionpersonassets')
            ->leftJoin('personassets', 'personassets.id', '=', 'transactionpersonassets.personasset_id')
            ->where(function($query) use ($transaction) {
                $query->where('transactionpersonassets.transaction_id', $transaction->id)
                        ->orWhere('transactionpersonassets.to_transaction_id', $transaction->id);
            })
            ->select(
                'transactionpersonassets.id',
                'transactionpersonassets.serial_no',
                'transactionpersonassets.sticker',
                'transactionpersonassets.remarks',
                'transactionpersonassets.qty',
                'personassets.code',
                'personassets.name',
                'personassets.brand'
            )
            ->oldest('transactionpersonassets.updated_at')
            ->get();
        // $profile = Profile::firstOrFail();
        $data = [
            'inv_id' => $transaction->id,
            'transaction'   =>  $transaction,
            'person'        =>  $person,
            'deals'         =>  $deals,
            'totalprice'    =>  $totalprice,
            'totalqty'      =>  $totalqty,
            'transactionpersonassets' => $transactionpersonassets,
            'type' => $type
            // 'profile'       =>  $profile,
        ];

        $seq = 0;
        $driver = 'unassigned';
        if($transaction->sequence) {
            $seq = $transaction->sequence * 1;
        }
        if($transaction->driver) {
            $driver = $transaction->driver;
        }

        $name = $driver.'_'.$seq.'_'.$transaction->del_postcode.'_'.$transaction->id.'_'.$person->cust_id.'_'.$person->company.'.pdf';
        $pdf = PDF::setOption('dpi', 100)
                ->setOption('page-size', 'A4')
                ->setOption('enable-javascript', true)
                ->setOption('javascript-delay', 3000)
                ->setOption('enable-smart-shrinking', false)
                ->setOption('print-media-type', true)
                ->setOption('margin-top', 10)
                ->setOption('margin-right', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 5)
                ->loadView('transaction.invoice', $data);
        return $pdf->download($name);
    }

    public function generateLogs($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transHistory = $transaction->revisionHistory;
        return view('transaction.log', compact('transaction', 'transHistory'));
    }

    // replicate the transaction particulars(int $transaction_id)
    public function replicateTransaction($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $replicated_transaction = $transaction->replicate();
        $replicated_transaction->order_date = Carbon::today()->toDateString();
        $replicated_transaction->delivery_date = Carbon::today()->toDateString();
        $replicated_transaction->status = 'Confirmed';
        $replicated_transaction->pay_status = 'Owe';
        $replicated_transaction->driver = null;
        $replicated_transaction->paid_by = '';
        $replicated_transaction->paid_at = '';
        if($transaction->person->cust_id[0] === 'P'){
            $replicated_transaction->po_no = null;
        }
        $replicated_transaction->updated_by = auth()->user()->name;
        // $replicated_transaction->note =
        $replicated_transaction->save();
        // dd($replicated_transaction->toArray());

        // replicate pricelist
        foreach($transaction->deals as $deal) {
            $replicated_deal = new Deal();
            $replicated_deal->item_id = $deal->item_id;
            $replicated_deal->transaction_id = $replicated_transaction->id;
            $replicated_deal->qty = $deal->qty;
            $replicated_deal->amount = $deal->amount;
            $replicated_deal->unit_price = $deal->unit_price;
            $replicated_deal->qty_status = $deal->qty_status;
            $replicated_deal->dividend = $deal->dividend;
            $replicated_deal->divisor = $deal->divisor;
            $replicated_deal->unit_cost = $deal->unit_cost;
            $replicated_deal->qty_before = $deal->qty_before;
            $replicated_deal->qty_after = $deal->qty_after;
            $replicated_deal->save();
        }
        return Redirect::action('TransactionController@edit', $replicated_transaction->id);
    }

    // status changing to verified owe/ paid
    public function changeStatus($id)
    {
        $transaction = Transaction::findOrFail($id);
        $status = $transaction->status;
        $pay_status = $transaction->pay_status;
        if($status == 'Delivered' and $pay_status == 'Owe'){
            $transaction->status = 'Verified Owe';
            $transaction->updated_by = Auth::user()->name;
            $transaction->save();
        }else if(($status == 'Verified Owe' or $status == 'Delivered') and $pay_status == 'Paid'){
            $transaction->status = 'Verified Paid';
            // $transaction->pay_method = 'cash';
            $transaction->updated_by = Auth::user()->name;
            $transaction->save();
        }
        // using redirect back since applied in different views
        return redirect()->back();
    }

    public function showPersonTransac($person_id)
    {
        $transactions = DB::table('transactions')
                ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                ->leftJoin('custcategories', 'people.custcategory_id', '=', 'custcategories.id')
                ->select(
                            'people.cust_id', 'people.company',
                            'people.name', 'people.id as person_id', 'transactions.del_postcode',
                            'transactions.status', 'transactions.driver', 'transactions.po_no',
                            'transactions.total_qty', 'transactions.pay_status',
                            'transactions.updated_by', 'transactions.updated_at', 'transactions.delivery_fee', 'transactions.id',
                            DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END)
                                            ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2) AS total'),
                            DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
                            'profiles.id as profile_id', 'transactions.gst', 'transactions.is_gst_inclusive', 'transactions.gst_rate',
                             'custcategories.name as custcategory'
                        )
                ->where('people.id', $person_id)
                ->orderBy('transactions.created_at', 'desc')
                ->take(5)
                ->get();
        return $transactions;
        // Transaction::with(['person', 'person.profile'])->wherePersonId($person_id)->latest()->take(5)->get();
    }

    // undo the cancelled transaction
    public function reverse($id)
    {
        $transaction = Transaction::findOrFail($id);
        $deals = Deal::where('transaction_id', $transaction->id)->where('qty_status', '3')->get();
        if($transaction->cancel_trace){
            $this->dealUndoDelete($transaction->id);
            $transaction->status = $transaction->cancel_trace;
            $transaction->cancel_trace = '';
            $transaction->updated_by = Auth::user()->name;
            if($transaction->dtdtransaction_id){
                $dtdtransaction = DtdTransaction::findOrFail($transaction->dtdtransaction_id);
                $dtdtransaction->status = 'Confirmed';
                $dtdtransaction->save();
            }
        }

        $transaction->save();

        $this->operationDatesSync($transaction->id);
        return Redirect::action('TransactionController@edit', $transaction->id);
    }

    // storing payment method and note in rpt
    public function rptDetail(Request $request, $id)
    {
        $paymethod = $request->input('paymethod');
        $note = $request->input('note');
        $transaction = Transaction::findOrFail($id);
        $transaction->pay_method = $paymethod;
        $transaction->note = $note;
        $transaction->save();
        return "Sucess updating transaction #" . $transaction->id;
    }

    // send invoice to D/ H email upon button clicked
    public function sendEmailInv($id)
    {
        $email_draft = GeneralSetting::firstOrFail()->DTDCUST_EMAIL_CONTENT;
        $transaction = Transaction::findOrFail($id);
        $self = Auth::user()->name;
        $deals = Deal::whereTransactionId($transaction->id)->get();
        $totalprice = DB::table('deals')->whereTransactionId($transaction->id)->sum('amount');
        $totalqty = DB::table('deals')->whereTransactionId($transaction->id)->sum('qty');
        $person = Person::findOrFail($transaction->person_id);

        $email = $person->email;

        if(! $email){
            Flash::error('Please set the email before sending');
            return Redirect::action('TransactionController@edit', $id);
        }else {
            if(strpos($email, ';') !== FALSE) {
                $email = explode(';', $email);
            }
        }

        $now = Carbon::now()->format('dmyhis');
        // $profile = Profile::firstOrFail();
        $data = [
            'inv_id' => $transaction->id,
            'transaction'   =>  $transaction,
            'person'        =>  $person,
            'deals'         =>  $deals,
            'totalprice'    =>  $totalprice,
            'totalqty'      =>  $totalqty,
        ];
        $name = 'Inv('.$transaction->id.')_'.$person->cust_id.'_'.$person->company.'('.$now.').pdf';
        $pdf = PDF::loadView('transaction.invoice', $data);
        $pdf->setPaper('a4');
        $sent = $pdf->save(storage_path('/invoice/'.$name));
        $store_path = storage_path('/invoice/'.$name);
        $sender = 'system@happyice.com.sg';
        $datamail = [
            'person' => $person,
            'transaction' => $transaction,
            'email_draft' => $email_draft,
            'self' => $self,
            'url' => 'http://www.happyice.com.sg',
        ];

        Mail::send('email.send_invoice', $datamail, function ($message) use ($email, $sender, $store_path, $transaction)
        {
            $message->from($sender);
            $message->subject('[Invoice - '.$transaction->id.'] Happy Ice - Thanks for Your Support');
            $message->setTo($email);
            $message->attach($store_path);
        });

        if($sent){
            Flash::success('Successfully Sent');
        }else{
            Flash::error('Please Try Again');
        }

        return Redirect::action('TransactionController@edit', $id);
    }

    // return invoice date freeze page()
    public function getFreezeInvoiceDate()
    {
        return view('transaction.freezedate');
    }

    // return invoice date freeze page api()
    public function getFreezeInvoiceDateApi()
    {
        $general_setting = GeneralSetting::firstOrFail();

        return $general_setting;
    }

    // store invoice freeze date()
    public function freezeInvoiceDate()
    {
        $general_setting = GeneralSetting::firstOrFail();

        $general_setting->INVOICE_FREEZE_DATE = request('freeze_date');

        $general_setting->save();

        DB::table('transactions')
            ->whereDate('delivery_date', '<=', $general_setting->INVOICE_FREEZE_DATE)
            ->update(['is_freeze' => 1]);

        DB::table('dtdtransactions')
            ->whereDate('delivery_date', '<=', $general_setting->INVOICE_FREEZE_DATE)
            ->update(['is_freeze' => 1]);

        return Redirect::action('TransactionController@freezeInvoiceDate');
    }

    // attach file on the invoice(int transaction_id)
    public function addInvoiceAttachment($transaction_id)
    {
        // dd('here');
        $transaction = Transaction::findOrFail($transaction_id);
        $file = request()->file('file');
        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
        Storage::put('inv_attachments/'.$transaction->person->cust_id.'/'.$name, file_get_contents($file->getRealPath()), 'public');
        $url = (Storage::url('inv_attachments/'.$transaction->person->cust_id.'/'.$name));
        // $file->put('inv_attachments/'.$transaction->person->cust_id.'/'.$name, 's3');
        // $file->move('inv_attachments/'.$transaction->person->cust_id.'/', $name);
        $transaction->invattachments()->create(['path' => $url]);
        // $transaction->invattachments()->create(['path' => $file->url()])
    }

    // remove attachment from the transaction invoice(int attachment_id)
    public function removeAttachment($attachment_id)
    {
        $invattachment = Invattachment::findOrFail($attachment_id);
        $filename = $invattachment->path;
        $path = public_path();
        if (!File::delete($path.$filename))
        {
            $invattachment->delete();
            return redirect()->action('TransactionController@edit', $invattachment->transaction->id);
        }else {
            $invattachment->delete();
            return redirect()->action('TransactionController@edit', $invattachment->transaction->id);
        }
    }

    // return email subscription page index()
    public function subscibeTransactionEmail()
    {
        return view('transaction.trans_subscription');
    }

    // return subscribed transaction email users()
    public function subscibeTransactionEmailApi()
    {
        $users = User::with(['roles', 'transSubscription'])->has('transSubscription')->get();

        return $users;
    }

    // return non subscribed transaction email users()
    public function nonSubscibeTransactionEmailApi()
    {
        $users = User::with(['roles'])->whereDoesntHave('transSubscription')->get();

        return $users;
    }

    // add user subscribed list ($user_id)
    public function addSubscriberTransactionEmailApi()
    {
        $trans_subs = new TransSubscription;
        $user = User::findOrFail(request('user_id'));
        $trans_subs->user_id = $user->id;
        $trans_subs->save();
        // $user->transSubscription()->save($trans_subs);
    }

    // remove user subscribed list(int $user_id)
    public function removeSubscriberTransactionEmailApi($user_id)
    {
        $user = User::findOrFail($user_id);

        $user->transSubscription()->delete();
    }

    // export account consolidate report from transactions index()
    public function exportAccConsolidatePdf()
    {

        $now = Carbon::now()->format('d-m-Y H:i');

        if(request('exportpdf') == 'do') {
            $title = 'Consolidated DO';
            $name = 'Consolidated_DO(' . $now . ').pdf';
        }else {
            $title = 'Consolidated Tax Invoice';
            $name = 'Consolidated_Tax_Invoice(' . $now . ').pdf';
        }

        $transactions = $this->getTransactionsData();
        if(!request('delivery_from') and !request('delivery_to')){
            $delivery_from = Carbon::today()->toDateString();
            $delivery_to = Carbon::today()->toDateString();
            $transactions = $transactions->where('transactions.delivery_date', '=', Carbon::today()->toDateString());
        }else {
            $delivery_from = request('delivery_from');
            $delivery_to = request('delivery_to');
        }
        $totalprice = $this->calDBTransactionTotal($transactions);
        $transactions = $transactions->oldest('transactions.created_at')->get();
        $person = Person::findOrFail(request('person_account'));

        $data = [
            'transactions' => $transactions,
            'totalprice' => $totalprice,
            'person' => $person,
            'delivery_from' => $delivery_from,
            'delivery_to' => $delivery_to,
            'title' => $title
        ];
        $pdf = PDF::loadView('transaction.acc_consolidate', $data);
        $pdf->setPaper('a4');
        $pdf->setOption('margin-top', 5);
        $pdf->setOption('margin-bottom', 5);
        $pdf->setOption('margin-left', 8);
        $pdf->setOption('margin-right', 8);
        $pdf->setOption('footer-right', 'Page [page]/[topage]');
        $pdf->setOption('dpi', 70);
        $pdf->setOption('page-width', '210mm');
        $pdf->setOption('page-height', '297mm');
        return $pdf->download($name);
    }

    // submit signature from transaction edit(int transaction_id)
    public function saveSignature($transaction_id)
    {
        $imgdata = request('data');
        $encoded_image = explode(",", $imgdata)[1];
        $decoded_image = base64_decode($encoded_image);
        $filename = $transaction_id.'_'.Carbon::now()->format('dmYHi').'.png';
        $file = file_put_contents($filename, $decoded_image);
        File::move(public_path().'/'.$filename, public_path().'/custsignature/'.$filename);

        $transaction = Transaction::findOrFail($transaction_id);
        $transaction->sign_url = "/custsignature/".$filename;
        $transaction->save();
    }

    // remove signature by transaction id given (int transaction_id)
    public function deleteSignature($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        File::delete(public_path().$transaction->sign_url);
        $transaction->sign_url = null;
        $transaction->save();
    }

    // revert transaction status to confirmed(int transaction_id)
    public function revertToConfirmStatus($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $transaction->status = 'Confirmed';
        $transaction->pay_status = 'Owe';
        $transaction->save();

        return Redirect::action('TransactionController@edit', $transaction->id);
    }

    // store delivery latlng whenever has chance(int transaction_id)
    public function storeDeliveryLatLng($id)
    {
        // dd($id, request()->all());
        $transaction = Transaction::findOrFail($id);
        $transaction->del_lat = request('lat');
        $transaction->del_lng = request('lng');
        $transaction->save();

        return $transaction;
        // dd($transaction);
    }

    // store delivery latlng whenever has chance()
    public function storeDeliveryLatLngArr(Request $request)
    {
        // dd($request->all()[0], request()->all());
        // foreach()
        $transaction = Transaction::findOrFail($id);
        $transaction->del_lat = request('lat');
        $transaction->del_lng = request('lng');
        $transaction->save();
    }


    // api to quick update driver(Request $request)
    public function driverQuickUpdate(Request $request)
    {
        $chosendriver = $request->driverchosen['name'];
        $model = Transaction::findOrFail($request->id);
        $searchtransaction = Transaction::where('delivery_date', '=', $request->delivery_date)->where('driver', $chosendriver)->max('sequence');

        $model->driver = $chosendriver;
        if($searchtransaction) {
            $model->sequence = $searchtransaction + 1;
        }else {
            $model->sequence = 1;
        }
        $model->save();

        return $model;
    }

    // api to quick update driver(Request $request)
    public function driverQuickUpdateJobAssign(Request $request)
    {
        $transaction = $request->transaction;
        $chosendriver = $transaction['driverchosen']['name'];
        $model = Transaction::findOrFail($transaction['id']);
        $searchtransaction = Transaction::where('delivery_date', '=', $transaction['delivery_date'])->where('driver', $chosendriver)->max('sequence');

        $model->driver = $chosendriver;
        if($searchtransaction) {
            $model->sequence = $searchtransaction + 1;
        }else {
            $model->sequence = 1;
        }
        $model->save();

        return $model;
    }

    // api for changing is important($id)
    public function isImportantChanged($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->is_important = !$transaction->is_important;
        $transaction->save();

        return $transaction;
    }

    // api for batch assign driver
    public function batchAssignDriver(Request $request)
    {
        $transactions = $request->transactions;
        $driver = $request->driver;

        if($transactions) {
            foreach($transactions as $transaction) {
                if(isset($transaction['check'])) {
                    if($transaction['check']) {
                        $model = Transaction::findOrFail($transaction['id']);
                        if($driver == '-1') {
                            $model->driver = null;
                        }else {
                            $model->driver = $driver;
                        }
                        $model->save();
                    }
                }
            }
        }
    }

    // api for batch job assign driver
    public function batchJobAssignDriver(Request $request)
    {
        $drivers = $request->drivers;
        $form_driver = $request->driver;
        $delivery_date = $request->delivery_date;

        if($drivers) {
            foreach($drivers as $driverindex => $driver) {
                foreach($driver['transactions'] as $transactionindex => $transaction) {
                    if(isset($transaction['check'])) {
                        if($transaction['check']) {
                            $max_sequence = 0;
                            $model = Transaction::findOrFail($transaction['id']);
                            $max_sequence = Transaction::where('driver', $form_driver)->whereDate('delivery_date', '=', $delivery_date)->max('sequence');
                            if($max_sequence) {
                                $model->sequence = $max_sequence + 1;
                            }else {
                                $model->sequence = 1;
                            }
                            if($form_driver == '-1') {
                                $model->driver = null;
                            }else {
                                $model->driver = $form_driver;
                            }
                            $model->save();
                        }
                    }
                    unset($drivers[$driverindex]['transactions'][$transactionindex]);
                }
            }
        }
    }

    // api for batch update delivery_date
    public function batchUpdateDeliveryDateJobAssign(Request $request)
    {
        $drivers = $request->drivers;
        $delivery_date = $request->delivery_date;

        if($drivers) {
            foreach($drivers as $driverindex => $driver) {
                foreach($driver['transactions'] as $transactionindex => $transaction) {
                    if(isset($transaction['check'])) {
                        if($transaction['check']) {
                            $model = Transaction::findOrFail($transaction['id']);
                            if($delivery_date) {
                                $this->operationDatesSync($model->id, $delivery_date);
                                $model->delivery_date = $delivery_date;
                            }
                            $model->sequence = null;
                            $model->updated_at = Carbon::now();
                            $model->updated_by = auth()->user()->name;
                            $model->save();
                        }
                    }
                    unset($drivers[$driverindex]['transactions'][$transactionindex]);
                }
            }
        }
    }

    // api for batch update delivery_date
    public function batchUpdateDeliveryDate(Request $request)
    {
        $transactions = $request->transactions;
        $delivery_date = $request->delivery_date;

        if($transactions) {
            foreach($transactions as $index => $transaction) {
                if(isset($transaction['check'])) {
                    if($transaction['check']) {
                        $model = Transaction::findOrFail($transaction['id']);

                        if($delivery_date) {
                            $this->operationDatesSync($model->id, $delivery_date);
                            $model->delivery_date = $delivery_date;
                        }
                        $model->sequence = null;
                        $model->updated_at = Carbon::now();
                        $model->updated_by = auth()->user()->name;
                        $model->save();
                    }
                }
            }
        }
    }

    // api for update transaction sequence
    public function updateTransactionSequence($id)
    {
        $sequence = request('sequence');
        $transaction = Transaction::findOrFail($id);

        $transaction->sequence = $sequence;
        $transaction->save();
/*
        $referTransactionsArr = Transaction::where('driver', $transaction->driver)->where('delivery_date', $transaction->delivery_date)->orderBy('sequence')->get();

        $collection = [];
        if($referTransactionsArr) {
            foreach($referTransactionsArr as $refertransaction) {
                $collection[$refertransaction->id] = $refertransaction->sequence;
            }
        }
        $collection[$transaction->id] = $sequence; */
    }

    // init transactions sequence
    public function initTransactionsSequence()
    {
        $drivers = request('drivers');
        $driverkey = request('driverkey');

        if($drivers) {
            $assignindex = 1;
            foreach($drivers[$driverkey]['transactions'] as $index => $transaction) {
                $trans = Transaction::findOrFail($transaction['id']);
                $trans->sequence = $assignindex;
                $trans->save();
                $drivers[$driverkey]['transactions'][$index]['sequence'] = $assignindex;
                // dd($transaction, $transaction['sequence']);
                $assignindex ++;
            }
        }
        return $drivers;
/*
        $newarr = [];
        $drivers = $request->drivers;
        $driverkey = $request->driverkey;

        if($drivers[$driverkey]) {
            $keys = array_column($drivers[$driverkey]['transactions'], 'sequence');
            array_multisort($keys, SORT_ASC, $drivers[$driverkey]['transactions']);
        }
        return $drivers; */
    }

    // refresh individual driver array job assign
    public function jobAssignRefreshDriver(Request $request)
    {
        $drivers = $request->drivers;
        $driverkey = $request->driverkey;

        if($drivers[$driverkey]) {
            $keys = array_column($drivers[$driverkey]['transactions'], 'sequence');
            array_multisort($keys, SORT_ASC, $drivers[$driverkey]['transactions']);
        }
        return $drivers;
    }

    // sort driver array job assign table locally
    public function sortJobAssignDriverTable(Request $request)
    {
        $driver = $request->driver;
        $sort_name = $request->sortName;
        $sort_by = $request->sortBy;
        $sort = SORT_ASC;

        if($sort_name) {
            if(strpos($sort_name, '.')) {
                $sort_name = explode('.', $sort_name)[1];
            }
        }
        if($sort_by) {
            $sort = SORT_ASC;
        }else {
            $sort = SORT_DESC;
        }
        // dd($sort_name, $driver['transactions']);

        $keys = array_column($driver['transactions'], $sort_name);
        array_multisort($keys, $sort, $driver['transactions']);

        return $driver;
    }

    // import excel to generate batch invoices
    public function importExcelTransaction(Request $request)
    {
        $file = '';
        $importStatusArr = [
            'success' => [],
            'item_failure' => [],
            'failure' => []
        ];
        if($file = request()->file('excel_file')){
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $resultfilename = 'Imported_'.$name;
            $loadfile = $file->move('import_excel', $name);
            // dd($resultfilename);

            // $excel = Excel::load($loadfile, function($reader) use ($name, $importStatusArr){
            $reader = Excel::load($loadfile, function($reader){})->get();
                $results = $reader->toArray();
                $headers = $reader->first()->toArray();
                $items = [];
                foreach($headers as $index => $header) {
                    if(strpos($index, '[') and strpos($index, ']')) {
                        $product = $this->getStringBetween($index, '[', ']');
                        if(strpos($product, ';')) {
                            $productArr = explode(';', $product);
                            $items[$index] = [
                                'product_id' => $productArr[0],
                                'multiplier' => isset($productArr[1]) ? $productArr[1] : 1,
                                'divisor' => isset($productArr[2]) ? $productArr[2] : 1,
                                'price' => isset($productArr[3]) ? $productArr[3] : ''
                            ];
                        }
                    }
                }
                // dd('here', $items, $headers);

                if($headers)
                  foreach($results as $resultindex => $result) {
                    $po_no = '';

                    if(!$result['customer_id'] and !$result['delivery_date']) {
                        // dd($results, $result['customer_id'], $result['delivery_date']);
                        continue;
                    }

                    if($person = Person::where('cust_id', $result['customer_id'])->first()) {

                        if($po_no = $result['po_no']) {
                            $po_no = trim($po_no);

                            if($person->cust_id[0] == 'P') {
                                $searchTransactionPo = Transaction::where('po_no', $po_no)->first();
                                if($searchTransactionPo) {
                                    array_push($importStatusArr['failure'], [
                                        'po_no' => $po_no,
                                        'cust_id' => $result['customer_id'],
                                        'del_postcode' => $result['del_postcode'],
                                        'reason' => 'Duplicated PO',
                                        'row_number' => $resultindex + 2
                                    ]);
                                    // dd('dup po');
                                    continue;
                                }
                            }
                        }
                        // dd('hereman');

                        $model = new Transaction();
                        if($cust_id = $result['customer_id']) {
                            $model->person_id = $person->id;
                            $model->person_code = $person->cust_id;
                            $model->gst = $person->profile->gst;
                            $model->gst_rate = $person->gst_rate;
                            $model->is_gst_inclusive = $person->is_gst_inclusive;
                            $model->po_no = $po_no;
                            $model->updated_by = 'system';
                            $model->created_by = 100129;
                        }
                        $del_postcode = isset($result['del_postcode']) ? $result['del_postcode'] : $person->del_postcode;
                        $del_address = isset($result['del_address']) ? $result['del_address'] : $person->del_address;
                        $order_date = isset($result['order_date']) ? Carbon::parse($result['order_date'])->toDateString() : Carbon::today()->toDateString();
                        $delivery_date = isset($result['delivery_date']) ? Carbon::parse($result['delivery_date'])->toDateString() : Carbon::today()->toDateString();
                        $total_amount = isset($result['total_amount']) ? $result['total_amount'] : '';
                        $delivery_fee = isset($result['delivery_fee']) ? $result['delivery_fee'] : '';
                        $attn_name = isset($result['attn_name']) ? $result['attn_name'] : $person->name;
                        $attn_contact = isset($result['attn_contact']) ? $result['attn_contact'] : $person->contact;
                        $attn_email = isset($result['attn_email']) ? $result['attn_email'] : $person->email;
                        $transremark = isset($result['transremark']) ? $result['transremark'] : '';
                        $payment_date = isset($result['payment_date']) ? $result['payment_date'] : '';
                        $dealArr = [];
                        $invoice_amount = 0;
                        $quantityArr = [];
                        $quoteArr = [];
                        $amountArr = [];

                        if($del_postcode) {
                            $model->del_postcode = $del_postcode;
                        }
                        if($del_address) {
                            $model->del_address = $del_address;
                        }
                        if($order_date) {
                            $model->order_date = $order_date;
                        }
                        if($delivery_date) {
                            $model->delivery_date = $delivery_date;
                        }
                        if($total_amount) {
                            $model->total = $total_amount;
                        }
                        if($attn_name) {
                            $model->name = $attn_name;
                        }
                        if($attn_contact) {
                            $model->contact = $attn_contact;
                        }
                        if($attn_email) {
                            $model->email = $attn_email;
                        }
                        if($transremark) {
                            $model->transremark = $transremark;
                            $model->is_important = 1;
                        }
                        if($payment_date) {
                            $model->paid_at = $payment_date;
                            $model->paid_by = auth()->user()->name;
                            $model->pay_method = 'cash';
                            $model->pay_status = 'Paid';
                        }
                        $model->status = 'Confirmed';
                        $model->save();

                        array_push($importStatusArr['success'], [
                            'transaction_id' => $model->id,
                            'cust_id' => $result['customer_id'],
                            'del_postcode' => $result['del_postcode'],
                            'po_no' => $po_no
                        ]);
// dd($items);
                        foreach($items as $itemindex => $itemExcel) {
                            // dd($del_postcode, $index,  $item, $result[$index], $items);
                            $priceObj = 0;
                            $inputQty = 0;
                            $qty = 0;
                            if($deal = $result[$itemindex]) {
                                $item = Item::where('product_id', $itemExcel['product_id'])->first();
                                $price = Price::where('item_id', $item->id)->where('person_id', $person->id)->first();

                                if(!$item) {
                                    array_push($importStatusArr['item_failure'], [
                                        'transaction_id' => $model ? $model->id : '',
                                        'cust_id' => $result['customer_id'],
                                        'del_postcode' => $del_postcode,
                                        'po_no' => $po_no,
                                        'item' => $itemExcel['product_id'],
                                        'qty' => $deal,
                                        'reason' => 'Product ID error'
                                    ]);
                                    continue;
                                }

                                $inputQty = $deal * $itemExcel['multiplier'].'/'.$itemExcel['divisor'];

                                $qty = $this->fraction($inputQty);

                                $item_price = 0;
                                if($itemExcel['price'] == '' or $itemExcel['price'] == null) {
                                    $item_price = $price->quote_price;
                                }else {
                                    $item_price = $itemExcel['price'];
                                }

                                $priceObj = $qty * $item_price;

                                $invoice_amount += $priceObj;

                                if($item) {
                                    array_push($dealArr, [
                                        'item_id' => $item->id,
                                        'qty' => $inputQty,
                                        'quote' => $item_price,
                                        'amount' => $priceObj
                                    ]);
                                    // dd($item, $dealArr, $quantityArr, $quoteArr, $amountArr);
                                }
                            }
                        }

                        if($delivery_fee) {
                            if($delivery_fee != 0) {
                                array_push($dealArr, [
                                    'item_id' => 308,
                                    'qty' => 1,
                                    'quote' => $delivery_fee,
                                    'amount' => $delivery_fee
                                ]);
                                $invoice_amount += $delivery_fee;
                            }
                        }

                        if($total_amount) {
                            $total_amount = number_format($total_amount, 2);
                            $invoice_amount = number_format($invoice_amount, 2);
                            $diff_amount = $total_amount - $invoice_amount;

                            if($invoice_amount != $total_amount and $diff_amount != 0) {
                                array_push($dealArr, [
                                    'item_id' => 21,
                                    'qty' => 1,
                                    'quote' => $diff_amount,
                                    'amount' => $diff_amount
                                ]);
                            }
                        }

                        $itemArrErrors = $this->syncDeal($model, $dealArr, 1);

                        if(count($itemArrErrors) > 0) {
                            foreach($itemArrErrors as $errorItem) {
                                array_push($importStatusArr['item_failure'], [
                                    'transaction_id' => $model ? $model->id : '',
                                    'cust_id' => $result['customer_id'],
                                    'del_postcode' => $del_postcode,
                                    'po_no' => $po_no,
                                    'item' => $errorItem,
                                    'reason' => 'Insufficient Stock'
                                ]);
                            }
                        }
                        // private function syncDeal($transaction, $quantities, $amounts, $quotes, $status)
                    }else {
                        array_push($importStatusArr['failure'], [
                            'po_no' => $po_no,
                            'cust_id' => $result['customer_id'],
                            'del_postcode' => $result['del_postcode'],
                            'reason' => 'Invalid Customer ID',
                            'syntax' => $result,
                            'row_number' => $resultindex + 2
                        ]);
                    }
                }
/*
                $excel = Excel::create($resultfilename, function ($excel) use ($importStatusArr) {
                    $excel->sheet('sheet1', function ($sheet) use ($importStatusArr) {
                        $sheet->setAutoSize(true);
                        $sheet->setColumnFormat(array(
                            'A:T' => '@'
                        ));
                        $sheet->loadView('transaction.upload_excel_result', compact('importStatusArr'));
                    });
                })->store('xls', public_path('/import_excel_result/')); */
            // });

            // dd($importStatusArr, $reader);

            Excel::create($resultfilename, function($excel) use ($importStatusArr) {
                $excel->sheet('sheet1', function($sheet) use ($importStatusArr) {
                    $sheet->setColumnFormat(array('A:P' => '@'));
                    $sheet->getPageSetup()->setPaperSize('A4');
                    $sheet->setAutoSize(true);
                    $sheet->loadView('transaction.upload_excel_result', compact('importStatusArr'));
                });
            })->store('xlsx', public_path('import_excel_result'));

            // if($excel) {
                // dd($excel);
                ImportTransactionExcel::create([
                    'upload_date' => Carbon::today()->toDateString(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_url' => '/import_excel/'.$name,
                    'result_url' => '/import_excel_result/'.$resultfilename.'.xlsx',
                    'uploaded_by' => auth()->user()->id
                ]);
            // }
        }
        // dd($importStatusArr);
        if(count($importStatusArr['failure']) > 0 or count($importStatusArr['item_failure']) > 0 or count($importStatusArr['success']) == 0) {
            return 'false';
        }else {
            return 'true';
        }
    }

    // retrieve upload excel history
    public function getLatestImportExcelHistory()
    {
        $histories = ImportTransactionExcel::with('uploader')
                    ->latest()
                    ->limit(5)
                    ->get();

        return $histories;
    }

    // batch update payment status
    public function batchUpdatePaymentStatus(Request $request)
    {
        $transactions = $request->transactions;
        $chosenArr = $request->chosen;
        if($transactions) {
            foreach($transactions as $index => $transaction) {
                if(isset($transaction['check'])) {
                    if($transaction['check']) {
                        $model = Transaction::findOrFail($transaction['id']);
                        if($chosenArr['pay_status']) {
                            if($chosenArr['pay_status'] == 'Paid') {
                                $model->pay_status = 'Paid';
                                $model->paid_at = $chosenArr['paid_at'];
                                $model->pay_method = $chosenArr['pay_method'];
                                $model->note = $chosenArr['note'];
                            }else {
                                $model->pay_status = 'Owe';
                                $model->paid_at = null;
                                $model->pay_method = null;
                            }
                            $model->updated_at = Carbon::now();
                            $model->updated_by = auth()->user()->name;
                            $model->save();
                        }
                    }
                }
            }
        }
    }

    // batch update status
    public function batchUpdateStatus(Request $request)
    {
        $transactions = $request->transactions;
        $chosenArr = $request->chosen;
        if($transactions) {
            $executedEntry = [

            ];
            foreach($transactions as $transaction) {
                if(isset($transaction['check'])) {
                    if($transaction['check']) {
                        DB::beginTransaction();
                        $model = Transaction::findOrFail($transaction['id']);
                        $prevStatus = $model->status;
                        $editStatus = $chosenArr['status'];
                        $entry = [
                            'id' => null,
                            'prevStatus' => $prevStatus,
                            'editStatus' => $editStatus,
                            'syncOrderDeals' => false,
                            'orderToActualDeals' => false,
                            'actualToOrderDeals' => false,
                            'deleteDeals' => false,
                            'nextStatus' => '',
                            'forceDelete' => false,
                            'qtyStatus' => 0
                        ];

                        if($prevStatus === $editStatus) {
                            continue;
                        }

                        switch($prevStatus) {
                            case 'Pending': {
                                switch($editStatus) {
                                    case 'Confirmed':
                                        $entry['syncOrderDeals'] = true;
                                        $entry['nextStatus'] = 'Confirmed';
                                        break;
                                    case 'Cancelled':
                                        $entry['nextStatus'] = 'Cancelled';
                                        break;
                                    case 'CancelRemove':
                                        $entry['nextStatus'] = 'Cancelled';
                                        $entry['forceDelete'] = true;
                                        break;
                                    default:
                                        $entry['nextStatus'] = $prevStatus;
                                }
                            }
                            break;
                            case 'Confirmed': {
                                switch($editStatus) {
                                    case 'Delivered':
                                        $entry['orderToActualDeals'] = true;
                                        $entry['nextStatus'] = 'Delivered';
                                        break;
                                    case 'Cancelled':
                                        $entry['deleteDeals'] = true;
                                        $entry['nextStatus'] = 'Cancelled';
                                        break;
                                    case 'CancelRemove':
                                        $entry['deleteDeals'] = true;
                                        $entry['nextStatus'] = 'Cancelled';
                                        $entry['forceDelete'] = true;
                                        break;
                                    default:
                                        $entry['nextStatus'] = $prevStatus;
                                }
                            }
                            break;
                            case 'Delivered': {
                                switch($editStatus) {
                                    case 'Confirmed':
                                        $entry['actualToOrderDeals'] = true;
                                        $entry['nextStatus'] = 'Confirmed';
                                        break;
                                    case 'Cancelled':
                                        $entry['deleteDeals'] = true;
                                        $entry['nextStatus'] = 'Cancelled';
                                        break;
                                    case 'CancelRemove':
                                        $entry['deleteDeals'] = true;
                                        $entry['nextStatus'] = 'Cancelled';
                                        $entry['forceDelete'] = true;
                                        break;
                                    default:
                                        $entry['nextStatus'] = $prevStatus;
                                }
                            }
                            break;
                            case 'Cancelled': {
                                switch($editStatus) {
/*
                                    case 'Confirmed':
                                        $entry['syncOrderDeals'] = true;
                                        $entry['nextStatus'] = 'Confirmed';
                                        $entry['qtyStatus'] = 1;
                                        break;
                                    case 'Delivered':
                                        $entry['orderToActualDeals'] = true;
                                        $entry['nextStatus'] = 'Delivered';
                                        $entry['qtyStatus'] = 2;
                                        break;
                                    case 'Cancelled':
                                        $entry['deleteDeals'] = true;
                                        $entry['nextStatus'] = 'Cancelled';
                                        $entry['qtyStatus'] = 3;
                                        break; */
                                    case 'CancelRemove':
                                        $entry['nextStatus'] = 'Cancelled';
                                        $entry['qtyStatus'] = 3;
                                        $entry['forceDelete'] = true;
                                        break;
                                    default:
                                        $entry['nextStatus'] = $prevStatus;
                                }
                            }
                            break;
                        }
                        // dd($entry['syncOrderDeals'], $entry['orderToActualDeals'], $entry['actualToOrderDeals'], $entry['deleteDeals']);

                        if($entry['syncOrderDeals']) {
                            $this->transactionDealSyncOrder($model->id);
                        }
                        if($entry['orderToActualDeals']) {
                            $this->dealOrder2Actual($model->id);
                        }
                        if($entry['actualToOrderDeals']) {
                            $this->dealActual2Order($model->id);
                        }
                        if($entry['deleteDeals']) {
                            $this->dealDeleteMultiple($model->id);
                        }

                        $model->status = $entry['nextStatus'];
                        $model->updated_at = Carbon::now();
                        $model->updated_by = auth()->user()->name;
                        $model->save();

                        array_push($executedEntry, $model->id);

                        $this->operationDatesSync($model->id);
                        // dd('hereman', $entry);
                        if($entry['forceDelete']) {
                            $this->destroyTransactionWithDeals($model->id);
                        }
                        DB::commit();
                    }
                }
            }
            return $executedEntry;
        }
    }

    private function dealMakeOrder($transaction_id, $qty_status)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $deals = $transaction->deals;

        if($deals) {
            foreach($deals as $deal) {
                $deal->qty_status = $qty_status;
                $deal->save();
            }
        }
    }

    private function transactionDealSyncOrder($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $deals = $transaction->deals;

        if($deals) {
            foreach($deals as $deal) {
                $this->dealSyncOrder($deal->item->id);
            }
        }
    }

    // update individual trans remark
    public function updateTransremarkById($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->transremark = request('transremark');
        $transaction->save();
    }

    // mass update qty status
    private function massUpdateQtyStatus($transaction_id, $qty_status)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        $deals = $transaction->deals;

        if($deals) {
            foreach($deals as $deal) {
                $deal->qty_status = $qty_status;
                $deal->save();
            }
        }
    }

    // retrieve transactions data ()
    private function getTransactionsData()
    {
        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'people.custcategory_id', '=', 'custcategories.id')
                        ->leftJoin('custcategory_groups', 'custcategories.custcategory_group_id', '=', 'custcategory_groups.id')
                        ->leftJoin('deliveryorders', 'deliveryorders.transaction_id', '=', 'transactions.id')
                        ->join('persontagattaches', 'persontagattaches.person_id', '=', 'people.id', 'left outer')
                        ->leftJoin('persontags', 'persontags.id', '=', 'persontagattaches.persontag_id')
                        ->leftJoin('users AS creator', 'creator.id', '=', 'transactions.created_by')
                        ->leftJoin('zones', 'zones.id', '=', 'people.zone_id');
                        // ->leftJoin($dupes_transaction, 'dupes_transactions.id', '=', 'transactions.id');
        $transactions = $transactions->select(
                                    'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id', 'people.operation_note', 'people.zone_id',
                                    'zones.name AS zone_name',
                                    'transactions.del_postcode','transactions.status', 'transactions.delivery_date', 'transactions.driver',
                                    'transactions.total_qty', 'transactions.pay_status', 'transactions.is_deliveryorder',
                                    'transactions.updated_by', 'transactions.updated_at', 'transactions.delivery_fee', 'transactions.id',
                                    'transactions.po_no', 'transactions.name', 'transactions.contact', 'transactions.del_address',
                                    'transactions.del_lat', 'transactions.del_lng', 'transactions.is_important', 'transactions.transremark', 'transactions.sequence', 'transactions.is_discard',
                                    DB::raw('DATE(transactions.delivery_date) AS del_date'),
                                    DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2) AS total'),
                                    'profiles.id as profile_id', 'transactions.gst', 'transactions.is_gst_inclusive', 'transactions.gst_rate',
                                    'custcategories.name as custcategory', 'custcategories.map_icon_file',
                                    DB::raw('DATE(deliveryorders.delivery_date1) AS delivery_date1'),
                                    'deliveryorders.po_no AS do_po', 'deliveryorders.requester_name', 'deliveryorders.pickup_location_name',
                                    'deliveryorders.delivery_location_name',
                                    DB::raw('SUBSTRING(people.area_group, 1, 1) AS west'),
                                    DB::raw('SUBSTRING(people.area_group, 3, 1) AS east'),
                                    DB::raw('SUBSTRING(people.area_group, 5, 1) AS others'),
                                    DB::raw('SUBSTRING(people.area_group, 7, 1) AS sup'),
                                    DB::raw('SUBSTRING(people.area_group, 9, 1) AS ops'),
                                    DB::raw('SUBSTRING(people.area_group, 11, 1) AS north'),
                                    'creator.id AS creator_id', 'creator.name AS creator_name'
                                );

        $transactions = $this->searchDBFilter($transactions);
        $transactions = $transactions->groupBy('transactions.id');
// dd($transactions->get());
        // add user profile filters
        $transactions = $this->filterUserDbProfile($transactions);

        // add user custcategory filters
        $transactions = $this->filterUserDbCustcategory($transactions);

        // toll to check is franchisee or not
        $transactions = $this->filterFranchiseeTransactionDB($transactions);

        // driver not able to see the invoices earlier than today
        // $transactions = $this->filterDriverView($transactions);

        return $transactions;
    }

    // retrieve transaction deals data
    private function getTransactionDeals($transactionId)
    {
        $transaction = Transaction::with(['deals', 'deals.item'])->findOrFail($transactionId);

        $deals = $transaction->deals;

        return $deals;
    }

    private function syncTransaction(Request $request)
    {
        $transaction = Auth::user()->transactions()->create($request->all());

        $this->syncItems($transaction, $request);
    }

    private function syncItems($transaction, $request)
    {
        if ( ! $request->has('item_list'))
        {
            $transaction->items()->detach();

            return;
        }

        $allItemsId = array();

        foreach ($request->item_list as $itemId)
        {
            if (substr($itemId, 0, 4) == 'new:')
            {
                $newItem = Item::create(['name'=>substr($itemId, 4)]);
                $allItemsId[] = $newItem->id;
                continue;
            }
            $allItemsId[] = $itemId;
        }

        $transaction->items()->sync($allItemsId);
    }

    // sync deals with email alert, deals and inventory deduction
    // private function syncDeal($transaction, $quantities, $amounts, $quotes, $status)
    private function syncDeal($transaction, $dealArr, $status)
    {

        // dd($dealArr);
        if($dealArr) {
            $errors = [];
            foreach($dealArr as $dealObj) {

                if($transaction->is_discard) {
                    $status = 99;
                }

                $qty = $dealObj['qty'];

                $dividend = 0;
                $divisor = 1;
                if(strpos($qty, '/') !== false) {
                    $dividend = explode('/', $qty)[0];
                    $divisor = explode('/', $qty)[1];
                    $qty = $this->fraction($qty);
                }

                if($qty != NULL or $qty != 0 ){
                    // dd('here0');
                    // inventory lookup before saving to deals
                    $item = Item::findOrFail($dealObj['item_id']);
                    // dd($dealObj['item_id'], $dealObj, $item);
                    $unitcost = Unitcost::whereItemId($item->id)->whereProfileId($transaction->person->profile_id)->first();
                    // inventory email notification for stock running low
                    if($item->email_limit and !$item->is_vending and $item->is_inventory){
                        if(($status == 1 and $this->calOrderEmailLimit($qty, $item)) or ($status == 2 and $this->calActualEmailLimit($qty, $item))){
                            if(! $item->emailed){
                                $this->sendEmailAlert($item);
                                // restrict only send 1 mail if insufficient
                                $item->emailed = true;
                                $item->save();
                            }
                        }else{
                            // reactivate email alert
                            $item->emailed = false;
                            $item->save();
                        }
                    }

                    if($status == 1 and $this->calOrderLimit($qty, $item)) {
                        // dd('here1');
                        array_push($errors, $item->product_id.' - '.$item->name);
                    }else if($status == 2 and $this->calActualLimit($qty, $item)) {
                        // dd('here2');
                        array_push($errors, $item->product_id.' - '.$item->name);
                    }else {
                        // dd('here3');
                        $deal = new Deal();
                        $deal->transaction_id = $transaction->id;
                        $deal->item_id = $dealObj['item_id'];
                        $deal->dividend = $dividend ? $dividend : $qty;
                        $deal->divisor = $divisor;
                        $deal->amount = $dealObj['amount'];
                        $deal->unit_price = $dealObj['quote'];
                        $deal->qty_status = $status;
                        $deal->unit_cost = $unitcost ? $unitcost->unit_cost : null;
                        $deal->qty = $qty;
                        if($status == 2 and $item->is_inventory === 1) {
                            $deal->qty_before = $item->qty_now;
                            $deal->qty_after = $item->qty_now - $qty;
                            $item->qty_now = $item->qty_now - $qty;
                            $item->save();
                            $this->loggingDebug($item, $deal);
                        }
                        $deal->save();
                        $this->dealSyncOrder($dealObj['item_id']);
                    }
                    // dd('here4');
                }
                // dd('here5');
            }
        }

        if($status == 2){
            $this->dealOrder2Actual($transaction->id);
        }

        $deals = Deal::leftJoin('items', 'items.id', '=', 'deals.item_id')->whereTransactionId($transaction->id);
        $deal_total = clone $deals;
        $deal_totalqty = clone $deals;
        $deal_total = $deal_total->sum('amount');
        $deal_totalqty = $deal_totalqty->where('items.is_inventory', 1)->sum('qty');
        $transaction->total = $deal_total;
        $transaction->total_qty = $deal_totalqty;
        $transaction->save();

        // sync dtdtransaction totals if valid
        if($transaction->dtdtransaction_id){
            $dtdtransaction = DtdTransaction::findOrFail($transaction->dtdtransaction_id);
            $dtdtransaction->total = $deal_total;
            $dtdtransaction->total_qty = $deal_totalqty;
            $dtdtransaction->save();
        }

        if(isset($errors)){
            if(count($errors) > 0){
                $errors_str = '';
                $errors_str = implode(" <br>", $errors);
                Flash::error('Stock Insufficient 缺货 (Please contact company 请联络公司): <br> '.$errors_str)->important();
            }
        }else{
            Flash::success('Successfully Added');
        }

        return isset($errors) ? $errors : '';
    }

    // email alert for stock insufficient
    private function sendEmailAlert($item)
    {
        $today = Carbon::now()->format('d-m-Y H:i');
        $emails = EmailAlert::where('status', 'active')->get();
        $email_list = array();
        foreach($emails as $email){
            $email_list[] = $email->email;
        }
        $email = array_unique($email_list);

        // $sender = 'daniel.ma@happyice.com.sg';
        $sender = 'system@happyice.com.sg';

        $data = [
            'product_id' => $item->product_id,
            'name' => $item->name,
            'remark' => $item->remark,
            'unit' => $item->unit,
            'qty_now' => $item->qty_now,
            'lowest_limit' => $item->lowest_limit,
            'email_limit' => $item->email_limit,
        ];

        Mail::send('email.stock_alert', $data, function ($message) use ($item, $email, $today, $sender)
        {
            $message->from($sender);
            $message->subject('Stock Insufficient Alert ['.$item->product_id.'-'.$item->name.'] - '.$today);
            $message->setTo($email);
        });
    }

    private function dealSyncOrder($item_id)
    {
        $item = Item::findOrFail($item_id);
        if($item->is_inventory) {
            $deals = Deal::where('qty_status', '1')->where('item_id', $item_id);
            $item->qty_order = $deals->sum('qty');
            $item->save();
        }
    }

    // convert order to actual deduction
    private function dealOrder2Actual($transaction_id)
    {
        $deals = Deal::where('qty_status', '1')->where('transaction_id', $transaction_id)->get();
        // dd($deals->toArray(), $transaction_id);
        foreach($deals as $deal){
            $item = Item::findOrFail($deal->item_id);
            $deal->qty_status = 2;
            if($item->is_inventory === 1) {
                $deal->qty_before = $item->qty_now;
                $deal->qty_after = $item->qty_now - $deal->qty;
                $item->qty_now = $item->qty_now - $deal->qty;
                $item->save();
            }
            $deal->save();
            $this->dealSyncOrder($item->id);
        }
    }

    // convert actual to order
    private function dealActual2Order($transaction_id)
    {
        $deals = Deal::where('qty_status', '2')->where('transaction_id', $transaction_id)->get();
        // dd($deals->toArray(), $transaction_id);
        foreach($deals as $deal){
            $item = Item::findOrFail($deal->item_id);
            $deal->qty_status = 1;
            if($item->is_inventory === 1) {
                $deal->qty_before = null;
                $deal->qty_after = null;
                $item->qty_now = $item->qty_now + $deal->qty;
                $item->save();
            }
            $deal->save();
            $this->dealSyncOrder($item->id);
        }
    }

    private function dealDeleteMultiple($transaction_id)
    {
        $deals = Deal::where('transaction_id', $transaction_id)->get();
        foreach($deals as $deal){
            $item = Item::findOrFail($deal->item_id);
            if($deal->qty_status == '1'){
                $deal->qty_status = 3;
                $deal->save();
            }else if($deal->qty_status == '2'){
                if($item->is_inventory === 1) {
                    $item->qty_now = $item->qty_now + $deal->qty;
                    $item->save();
                    $this->loggingDebug($item, $deal);
                }
                $deal->qty_status = 3;
                $deal->save();
            }
            $this->dealSyncOrder($item->id);
        }
    }

    private function destroyTransactionWithDeals($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        $this->dealDeleteMultiple($transaction_id);

        $transaction->deals()->delete();

        $transaction->delete();
    }

    private function dealUndoDelete($transaction_id)
    {
        $deals = Deal::where('transaction_id', $transaction_id)->where('qty_status', '3')->get();
        $transaction = Transaction::findOrFail($transaction_id);
        if($transaction->cancel_trace === 'Confirmed'){
            foreach($deals as $deal){
                $item = Item::findOrFail($deal->item_id);
                $deal->qty_status = 1;
                $deal->save();
                $this->dealSyncOrder($item->id);
            }
        }else if($transaction->cancel_trace === 'Delivered' or $transaction->cancel_trace === 'Verified Owe' or $transaction->cancel_trace === 'Verified Paid'){
            foreach($deals as $deal){
                $item = Item::findOrFail($deal->item_id);
                $deal->qty_status = 2;
                $deal->save();
                if($item->is_inventory === 1) {
                    $deal->qty_before = $item->qty_now;
                    $deal->qty_after = $item->qty_now - $deal->qty;
                    $item->qty_now = $item->qty_now - $deal->qty;
                    $item->save();
                    $deal->save();
                    $this->loggingDebug($item, $deal);
                }
            }
        }
    }

    private function calOrderLimit($qty, $item)
    {
        $qty = bcdiv($qty, 1, 4);
        if(($item->qty_now - $item->qty_order - $qty < $item->lowest_limit ? $item->lowest_limit : 0) and ($qty > 0) and ($item->is_inventory === 1)) {
            return true;
        }else {
            return false;
        }
    }

    private function calActualLimit($qty, $item)
    {
        if(strstr($qty, '/')) {
            $qty = $this->fraction($qty);
        }
        $qty = bcdiv($qty, 1, 4);
        if(($item->qty_now - $qty < $item->lowest_limit ? $item->lowest_limit : 0) and ($qty > 0) and ($item->is_inventory === 1)) {
            return true;
        }else {
            return false;
        }
    }

    private function calOrderEmailLimit($qty, $item)
    {
        if(strstr($qty, '/')) {
            $qty = $this->fraction($qty);
        }
        $qty = bcdiv($qty, 1, 4);
        if(($item->qty_now - $item->qty_order - $qty < $item->email_limit) and ($qty > 0) and ($item->is_inventory === 1)) {
            return true;
        }else {
            return false;
        }
    }

    private function calActualEmailLimit($qty, $item)
    {
        if(strstr($qty, '/')) {
            $qty = $this->fraction($qty);
        }
        $qty = bcdiv($qty, 1, 4);
        if(($item->qty_now - $qty < $item->email_limit) and ($qty > 0) and ($item->is_inventory === 1)) {
            return true;
        }else{
            return false;
        }
    }

    private function fraction($frac)
    {
        $fraction = explode("/",$frac);
        if($fraction[1] != 0) {
            return $fraction[0]/$fraction[1];
        }
        return "Division by zero error!";
    }

    private function syncOrder($transaction_id)
    {
        $transaction = Transaction::find($transaction_id);
        $dtdtransaction = DtdTransaction::where('transaction_id', $transaction_id)->first();
        // sync to be replaced <-> original
        $this->transactionXChange($dtdtransaction, $transaction);
        // find and sync deals
        $deals = Deal::where('transaction_id', $transaction_id)->get();
        $dtddeals = DtdDeal::where('transaction_id', $dtdtransaction->id)->get();

        if(count($deals) != count($dtddeals)){
            $deal_arr = array();
            $dtddeal_arr = array();
            $dtddeals = DtdDeal::where('transaction_id', $dtdtransaction->id)->get();
            foreach($dtddeals as $dtddeal){
                array_push($dtddeal_arr, $dtddeal->deal_id);
            }

            $dealresults = Deal::where('transaction_id', $dtdtransaction->transaction_id)->whereNotIn('id', $dtddeal_arr)->get();
            foreach($dealresults as $dealresult){
                $unitcost = Unitcost::whereProfileId($dtdtransaction->person->profile_id)->whereItemId($dealresult->item_id)->first();
                $dtddeal = new DtdDeal();
                $dtddeal->item_id = $dealresult->item_id;
                $dtddeal->transaction_id = $dtdtransaction->id;
                $dtddeal->dividend = $dealresult->dividend;
                $dtddeal->divisor = $dealresult->divisor;
                $dtddeal->amount = $dealresult->amount;
                $dtddeal->unit_price = $dealresult->unit_price;
                $dtddeal->qty_status = $dealresult->qty_status;
                $dtddeal->deal_id = $dealresult->id;
                $item = Item::find($dealresult->item_id);
                if($unitcost) {
                    $dtddeal->unit_cost = $unitcost->unit_cost;
                }
                if($item->is_inventoy) {
                    $dtddeal->qty = $dealresult->qty;
                }
                $dtddeal->save();
            }

            $deals = Deal::where('transaction_id', $transaction_id)->get();
            foreach($deals as $deal){
                array_push($deal_arr, $deal->id);
            }

            $dtdresults = DtdDeal::where('transaction_id', $dtdtransaction->id)->whereNotIn('deal_id', $deal_arr)->get();
            foreach($dtdresults as $dtdresult){
                $dtdresult->delete();
            }
        }
    }

    private function dtdDelUpdate($transaction)
    {
        $dtdtransaction = DtdTransaction::where('id', $transaction->dtdtransaction_id)->first();
        if($dtdtransaction){
            $dtdtransaction->status = 'Delivered';
            $dtdtransaction->save();
        }
    }

    private function dtdPaidUpdate($transaction)
    {
        $dtdtransaction = DtdTransaction::where('id', $transaction->dtdtransaction_id)->first();
        if($dtdtransaction){
            $dtdtransaction->pay_status = 'Paid';
            $dtdtransaction->save();
        }
    }

    // exchange transaction attributes
    private function transactionXChange($transactionSync, $transactionOri)
    {
        $transactionSync->total = $transactionOri->total;
        $transactionSync->total_qty = $transactionOri->total_qty;
        $transactionSync->delivery_date = $transactionOri->delivery_date;
        $transactionSync->del_postcode = $transactionOri->del_postcode;
        $transactionSync->status = $transactionOri->status;
        $transactionSync->transremark = $transactionOri->transremark;
        $transactionSync->updated_by = $transactionOri->updated_by;
        $transactionSync->pay_status = $transactionOri->pay_status;
        $transactionSync->person_code = $transactionOri->person_code;
        $transactionSync->person_id = $transactionOri->person_id;
        $transactionSync->order_date = $transactionOri->order_date;
        $transactionSync->del_address = $transactionOri->del_address;
        $transactionSync->name = $transactionOri->name;
        $transactionSync->po_no = $transactionOri->po_no;
        $transactionSync->save();
    }

    // pass value into filter search (collection, collection request) [query]
    private function searchFilter($transactions, Request $request)
    {
        if($request->id){
            $transactions = $transactions->searchId($request->id);
        }
        if($request->cust_id){
            $transactions = $transactions->searchCustId($request->cust_id);
        }
        if($request->company){
            $transactions = $transactions->searchCompany($request->company);
        }
        if($request->status){
            $transactions = $transactions->status($request->status);
        }
        if($request->pay_status){
            $transactions = $transactions->payStatus($request->pay_status);
        }
        if($request->updated_by){
            $transactions = $transactions->updatedBy($request->updated_by);
        }
        if($request->updated_at){
            $transactions = $transactions->updatedAt($request->updated_at);
        }
        if($request->delivery_date){
            $transactions = $transactions->searchDeliveryDate($request->delivery_date);
        }
        if($request->driver){
            $transactions = $transactions->driver($request->driver);
        }
        if($request->profile){
            $transactions = $transactions->searchProfile($request->profile);
        }
        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }
        return $transactions;
    }

    // pass value into filter search for DB (collection, collection request) [query]
    private function searchDBFilter($transactions)
    {
        // dd(request()->all());
        if(request('transaction_id')){
            $transactions = $transactions->where('transactions.id', 'LIKE', '%'.request('transaction_id').'%');
        }
        if(request('transactions_row')) {
            $transaction_rows = array_map('trim',explode("\n", request('transactions_row')));
            $transactions = $transactions->whereIn('transactions.id', $transaction_rows);
        }
        if(request('po_row')) {
            $po_row = array_map('trim',explode("\n", request('po_row')));
            $transactions = $transactions->whereIn('transactions.po_no', $po_row);
        }
        if(request('cust_id')){
            if(request('strictCustId')) {
                $transactions = $transactions->where('people.cust_id', 'LIKE', request('cust_id').'%');
            }else {
                $transactions = $transactions->where('people.cust_id', 'LIKE', '%'.request('cust_id').'%');
            }
        }
        if(request('company')){
            $com = request('company');
            $transactions = $transactions->where(function($query) use ($com){
                $query->where('people.company', 'LIKE', '%'.$com.'%')
                        ->orWhere(function ($query) use ($com){
                            $query->where('people.cust_id', 'LIKE', 'D%')
                                    ->where('people.name', 'LIKE', '%'.$com.'%');
                        });
                });
        }
        if(request('status')){
            $transactions = $transactions->where('transactions.status', 'LIKE', '%'.request('status').'%');
        }
        if (request('statuses')) {
            $statuses = request('statuses');
            if(in_array("Delivered", $statuses)) {
                array_push($statuses, 'Verified Owe', 'Verified Paid');
            }

            $transactions = $transactions->whereIn('transactions.status', $statuses);
        }
        if(request('pay_status')){
            $transactions = $transactions->where('transactions.pay_status', 'LIKE', '%'.request('pay_status').'%');
        }
        if(request('updated_by')){
            $transactions = $transactions->where('transactions.updated_by', 'LIKE', '%'.request('updated_by').'%');
        }
        if(request('updated_at')){
            $transactions = $transactions->where('transactions.updated_at', 'LIKE', '%'. request('updated_at').'%');
        }

        if(!auth()->user()->hasRole('hd_user')) {
            if(request('delivery_from') === request('delivery_to')){
                if(request('delivery_from') != '' and request('delivery_to') != ''){
                    $transactions = $transactions->whereDate('transactions.delivery_date', '=', request('delivery_from'));
                }
            }else{
                if(request('delivery_from')){
                    $transactions = $transactions->whereDate('transactions.delivery_date', '>=', request('delivery_from'));
                }
                if(request('delivery_to')){
                    $transactions = $transactions->whereDate('transactions.delivery_date', '<=', request('delivery_to'));
                }
            }
        }

        if(request('driver') != ''){
            if(request('driver') == '-1') {
                $transactions = $transactions->where(function($query) {
                    $query->whereNull('transactions.driver')->orWhere('transactions.driver', '=', '');
                });
            }else {
                $transactions = $transactions->where('transactions.driver', 'LIKE', '%'.request('driver').'%');
            }
        }
        if(request('profile_id')){
            $transactions = $transactions->where('profiles.id', request('profile_id'));
        }
/*
        if(request('custcategory')) {
            $transactions = $transactions->where('custcategories.id', request('custcategory'));
        }
        if (request('custcategory')) {
            $custcategory = request('custcategory');
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            $transactions = $transactions->whereIn('custcategories.id', $custcategory);
        }*/

        if(request('custcategory')) {
            $custcategories = request('custcategory');
            if (count($custcategories) == 1) {
                $custcategories = [$custcategories];
            }
            if(request('exclude_custcategory')) {
                $transactions = $transactions->whereNotIn('custcategories.id', $custcategories);
            }else {
                $transactions = $transactions->whereIn('custcategories.id', $custcategories);
            }
        }

        if(request('custcategory_group')) {
            $custcategoryGroups = request('custcategory_group');
            if (count($custcategoryGroups) == 1) {
                $custcategoryGroups = [$custcategoryGroups];
            }
            if(request('exclude_custcategory_group')) {
                // dd('here');
                $transactions = $transactions->whereNotIn('custcategory_groups.id', $custcategoryGroups);
            }else {
                $transactions = $transactions->whereIn('custcategory_groups.id', $custcategoryGroups);
            }
        }

        // add in franchisee checker

        if (auth()->user()->hasRole('franchisee') or auth()->user()->hasRole('hd_user') or auth()->user()->hasRole('watcher')) {
            $transactions = $transactions->whereIn('people.franchisee_id', [auth()->user()->id]);
        } else if(auth()->user()->hasRole('subfranchisee')) {
            $transactions = $transactions->whereIn('people.franchisee_id', [auth()->user()->master_franchisee_id]);
        } else if(request('franchisee_id') != null) {
            if(request('franchisee_id') != 0) {
                $transactions = $transactions->where('people.franchisee_id', request('franchisee_id'));
            }else {
                $transactions = $transactions->where('people.franchisee_id', 0);
            }
        }
/*
        if (request('person_active')) {
            $transactions = $transactions->where('people.active', request('person_active'));
        } */
        if (request('person_active')) {
            $personstatus = request('person_active');
            if (count($personstatus) == 1) {
                $personstatus = [$personstatus];
            }
            $transactions = $transactions->whereIn('people.active', $personstatus);
        }

        if(request('do_po')) {
            $transactions = $transactions->where('deliveryorders.po_no', 'LIKE', '%'.request( 'do_po').'%');
        }

        if(request('requester_name')) {
            $transactions = $transactions->where('deliveryorders.requester_name', 'LIKE', '%'.request('requester_name').'%');
        }

        if(request('pickup_location_name')) {
            $transactions = $transactions->where('deliveryorders.pickup_location_name', 'LIKE', '%'.request('pickup_location_name').'%');
        }

        if(request('delivery_location_name')) {
            $transactions = $transactions->where('deliveryorders.delivery_location_name', 'LIKE', '%'.request('delivery_location_name').'%');
        }

        if(auth()->user()->hasRole('hd_user') or request('cust_id') == 'B301') {
            if(request('requested_from') === request('requested_to')){
                if(request('requested_from') != '' and request('requested_to') != ''){
                    $transactions = $transactions->where('deliveryorders.delivery_date1', '=', request('requested_from'));
                }
            }else{
                if(request('requested_from')){
                    $transactions = $transactions->where('deliveryorders.delivery_date1', '>=', request('requested_from'));
                }
                if(request('requested_to')){
                    $transactions = $transactions->where('deliveryorders.delivery_date1', '<=', request('requested_to'));
                }
            }
        }

        if($area_groups = request('area_groups')) {

            if (count($area_groups) == 1) {
                $area_groups = [$area_groups];
            }

            $transactions = $transactions->where(function($query) use ($area_groups) {

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
        if(request('po_no')) {
            $transactions = $transactions->where('transactions.po_no', 'LIKE',  '%'.request('po_no').'%');
        }

        if(request('contact')) {
            $transactions = $transactions->where('transactions.contact', 'LIKE',  '%'.request('contact').'%');
        }

        if(request('is_gst_inclusive')) {
            $transactions = $transactions->where('transactions.is_gst_inclusive', request('is_gst_inclusive') == 'true' ? 1 : 0);
        }

        if(request('gst_rate')) {
            $transactions = $transactions->where('transactions.gst_rate', request('gst_rate'));
        }

        if(request('tags')) {
            $tags = request('tags');
            if (count($tags) == 1) {
                $tags = [$tags];
            }
            $transactions = $transactions->whereIn('persontags.id', $tags);
        }

        if(request('creator_id')) {
            $transactions = $transactions->where('creator.id', request('creator_id'));
        }

        if($item_id = request('item_id')) {
            $items = $item_id;
            if (count($items) > 0) {
                $itemStr = implode(",", $items);
            }else {
                $itemStr = $items;
            }

            // dd($items, $itemStr);
            $transactions = $transactions->whereRaw('transactions.id IN (SELECT transaction_id FROM deals WHERE item_id IN ('.$itemStr.'))');
        }

        if($accountManager = request('account_manager')) {
            $transactions = $transactions->where('people.account_manager', $accountManager);
        }

        if($zone_id = request('zone_id')) {
            $transactions = $transactions->where('people.zone_id', $zone_id);
        }

        if(request('sortName')){
            $transactions = $transactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $transactions;
    }

    // logic applicable for driver on transactions view
    private function filterDriverView($query)
    {
        if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
            $query = $query->where(function($query) {
                $query->where('transactions.driver', auth()->user()->name)
                    ->orWhere('transactions.driver', null);
            });
            // $query = $query->whereDate('transactions.delivery_date', '>=', Carbon::today()->toDateString());
        }

        return $query;
    }

    // calculating gst and non for delivered total
    private function calTransactionTotal($query)
    {
        $total_amount = 0;
        $nonGst_amount = 0;
        $gst_exclusive = 0;
        $gst_inclusive = 0;
        $query1 = clone $query;
        $query2 = clone $query;
        $query3 = clone $query;

        $nonGst_amount = $query1->with('person.profile')->whereHas('person.profile', function($query1){
                            $query1->where('gst', 0);
                        })->sum(DB::raw('ROUND(total, 2)'));

        $gst_exclusive = $query2->whereHas('person', function($query2) {
                                    $query2->where('is_gst_inclusive', 0);
                                })
                                ->sum(DB::raw('ROUND((total * person.gst_rate/100), 2)'));

        $gst_inclusive = $query3->whereHas('person', function($query3) {
                                    $query3->where('is_gst_inclusive', 1);
                                })
                                ->sum(DB::raw('ROUND(total, 2)'));

        $total_amount = $nonGst_amount + $gst_exclusive + $gst_inclusive;
        return $total_amount;
    }

    // calculating gst and non for delivered total
    private function calDBTransactionTotal($query)
    {
        // dd($query->select('transactions.total')->get());
        $total_amount = 0;
        $nonGst_amount = 0;
        $gst_exclusive = 0;
        $gst_inclusive = 0;
        $query1 = clone $query;
        $query2 = clone $query;
        $query3= clone $query;

        $nonGst_amount = $query1->where('transactions.gst', 0)->where('transactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND(transactions.total, 2)'));
        $gst_exclusive = $query2->where('transactions.gst', 1)->where('transactions.is_gst_inclusive', 0)->where('transactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND((transactions.total * (100 + transactions.gst_rate)/100), 2)'));
        $gst_inclusive = $query3->where('transactions.gst', 1)->where('transactions.is_gst_inclusive', 1)->where('transactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND(transactions.total, 2)'));
        dd($nonGst_amount, $gst_exclusive, $gst_inclusive);

        $total_amount = $nonGst_amount + $gst_exclusive + $gst_inclusive;

        return $total_amount;
    }

    // calculate total in arr
    private function calArrTransactionTotal($query)
    {
        $total_query = clone $query;

        $totalsArr = $total_query->get();

        $total = 0;

        foreach($totalsArr as $totalArr) {
            $total += $totalArr->total;
        }

        return $total;
    }

    // calculate delivery fees total
    private function calDBDeliveryTotal($query)
    {
        $query3 = clone $query;
        $delivery_fee = $query3->where('transactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND(transactions.delivery_fee, 2)'));
        return $delivery_fee;
    }

    // validate is whether vending machine for the final state udpate(Formrequest $request, int transaction_id)
    private function vendingMachineValidation($request, $transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        // dd($transaction->person->toArray());
        if($transaction->person->is_vending === 1) {
            $this->validate($request, [
                'digital_clock' => 'integer',
                'balance_coin' => 'numeric'
            ], [
                'digital_clock.integer' => 'Digital clock must be in integer',
                'balance_coin.numeric' => 'Balance coin must be in numbers'
            ]);
        }

        // sales count and sales amount figure must be filled
        if($transaction->person->is_dvm and auth()->user()->hasRole('driver')) {
            $this->validate($request, [
                'sales_count' => 'required',
                'sales_amount' => 'required'
            ], [
                'sales_count.required' => 'Sales Count (pcs) must be filled',
                'sales_amount.required' => 'Sales Amount ($) must be filled'
            ]);
            if($request->sales_count <= 0 and $request->sales_amount <= 0) {
                Flash::error('Sales count and Sales amount must be greater than 0');
            }
        }
    }

    // lookup and sync ftransaction if applicable(collection transaction)
/*    private function syncFtransactionsAndTransactions($transaction)
    {
        $ftransaction = Ftransaction::updateOrCreate([
            'transaction_id' => $transaction->id
        ], [
            'ftransaction_id' => $transaction->ftransaction_id ? $transaction->ftransaction_id : $this->getFtransactionIncrement($transaction->person->franchisee_id),
            'total' => $transaction->total ? $transaction->total : 0,
            'delivery_date' => $transaction->delivery_date,
            'status' => $transaction->status,
            'transremark' => $transaction->transremark,
            'updated_by' => $transaction->updated_by,
            'pay_status' => $transaction->pay_status,
            'person_code' => $transaction->person_code,
            'person_id' => $transaction->person_id,
            'order_date' => $transaction->order_date,
            'driver' => $transaction->driver,
            'paid_by' => $transaction->paid_by,
            'del_address' => $transaction->del_address,
            'name' => $transaction->name,
            'po_no' => $transaction->po_no,
            'total_qty' => $transaction->total_qty,
            'pay_method' => $transaction->pay_method,
            'note' => $transaction->note,
            'paid_at' => $transaction->paid_at,
            'cancel_trace' => $transaction->cancel_trace,
            'contact' => $transaction->contact,
            'del_postcode' => $transaction->del_postcode,
            'delivery_fee' => $transaction->delivery_fee,
            'bill_address' => $transaction->bill_address,
            'digital_clock' => $transaction->digital_clock,
            'analog_clock' => $transaction->analog_clock,
            'balance_coin' => $transaction->balance_coin,
            'is_freeze' => $transaction->is_freeze,
            'is_required_analog' => $transaction->is_required_analog,
            'franchisee_id' => $transaction->person->franchisee_id,
        ]);
    }*/

    // save do data by transaction id(transaction_id)
    private function saveDoByTransactionid($transaction_id)
    {
        $do = Deliveryorder::where('transaction_id', $transaction_id)->firstOrFail();
        // dd(request()->all(), request('to_happyice'), request()->has('to_happyice'));
            // $do->update($request->all());
        $do->update([
            'job_type' => request('job_type'),
            'po_no' => request('po_no'),
            'pickup_date' => request('pickup_date'),
            'pickup_timerange' => request('pickup_timerange'),
            'pickup_attn' => request('pickup_attn'),
            'pickup_contact' => request('pickup_contact'),
            'pickup_postcode' => request('pickup_postcode'),
            'pickup_location_name' => request('pickup_location_name'),
            'pickup_address' => request('pickup_address'),
            'pickup_comment' => request('pickup_comment'),
            'delivery_date1' => request('pickup_date'),
            'delivery_timerange' => request('delivery_timerange'),
            'delivery_attn' => request('delivery_attn'),
            'delivery_contact' => request('delivery_contact'),
            'delivery_postcode' => request('delivery_postcode'),
            'delivery_location_name' => request('delivery_location_name'),
            'delivery_address' => request('delivery_address'),
            'delivery_comment' => request('delivery_comment'),
            'from_happyice' => request('from_happyice') == 'true' ? 1 : 0,
            'to_happyice' => request('to_happyice') == 'true' ? 1 : 0,
            'requester_name' => request('requester_name'),
            'requester_contact' => request('requester_contact'),
            'requester_notification_emails' => request('requester_notification_emails')
        ]);
    }

    // update transaction person assets into asset movement($transaction_id)
    private function updateIsWarehouseTransactionpersonassets($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        // dd(Transactionpersonasset::where('transaction_id', $transaction_id)->get()->toArray(), Transactionpersonasset::where('to_transaction_id', $transaction_id)->get()->toArray());

        if($transaction->deliveryorder->from_happyice == 1 and $transaction->deliveryorder->to_happyice == 0) {
            // dd('here1');
            $transactionpersonassets = Transactionpersonasset::where('to_transaction_id', $transaction_id)->get();
            // dd( $transactionpersonassets->toArray(), $transaction->toArray());
            foreach($transactionpersonassets as $transactionpersonasset) {
                $transactionpersonasset->dateout = $transaction->delivery_date;
                $transactionpersonasset->is_warehouse = 0;
                $transactionpersonasset->thru_warehouse = 1;
                $transactionpersonasset->save();
            }
        }

        if($transaction->deliveryorder->to_happyice == 1 and $transaction->deliveryorder->from_happyice == 0) {
            // dd('here2');
            $transactionpersonassets = Transactionpersonasset::where('transaction_id', $transaction_id)->get();
            // dd( $transactionpersonassets->toArray(), $transaction->toArray());
            foreach($transactionpersonassets as $transactionpersonasset) {
                $transactionpersonasset->datein = Carbon::now();
                $transactionpersonasset->is_warehouse = 1;
                $transactionpersonasset->thru_warehouse = 1;
                $transactionpersonasset->save();
            }
        }
        // dd('here3');

        if($transaction->deliveryorder->requester_notification_emails) {
            $this->sendDoDeliveredEmailAlert($transaction->id);
        }
    }

    // send do confirmation email (int transaction_id)
    private function sendDoConfirmEmailAlert($transaction_id)
    {
        $today = Carbon::now()->format('Y-m-d');
        $emails = EmailAlert::where('status', 'active')->get();
        $email_list = array();
        foreach ($emails as $email) {
            $email_list[] = $email->email;
        }
        $email = array_unique($email_list);
        // $email = 'leehongjie91@gmail.com';

        // $sender = 'daniel.ma@happyice.com.sg';
        $sender = 'system@happyice.com.sg';

        $transaction = Transaction::findOrFail($transaction_id);

        $data = [
            'transaction' => $transaction,
        ];

        Mail::send('email.do_confirm_alert', $data, function ($message) use ($transaction, $email, $today, $sender) {
            $message->from($sender);
            $message->subject('HaagenDaz Job Confirmed '.$transaction->deliveryorder->pickup_date.' ['.$transaction->id.']');
            $message->setTo($email);
        });
    }

    // send do delivered email (int transaction_id)
    private function sendDoDeliveredEmailAlert($transaction_id)
    {

        $transaction = Transaction::findOrFail($transaction_id);
        $today = Carbon::now()->format('Y-m-d');
        $email_list = array();
        $email_list = explode(";", $transaction->deliveryorder->requester_notification_emails);

        $email = array_unique($email_list);
        // $email = 'leehongjie91@gmail.com';

        // $sender = 'daniel.ma@happyice.com.sg';
        $sender = 'system@happyice.com.sg';

        $data = [
            'transaction' => $transaction,
        ];

        Mail::send('email.do_confirm_alert', $data, function ($message) use ($transaction, $email, $today, $sender) {
            $message->from($sender);
            $message->subject('HaagenDaz Job Delivered '.$today.' ['.$transaction->id.']');
            $message->setTo($email);
        });
    }

    // Logging purpose
    private function loggingDebug($item, $deal)
    {
        if($item->id === 356) {
            Log::info($deal->transaction_id.', current: '.$item->qty_now.', qty: '.$deal->qty.', before: '.$deal->qty_before.', after: '.$deal->qty_after);
        }

    }

    // return string between two symbols or char
    private function getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    private function operationDatesSync($transaction_id, $newdate = null, $prevdate = null)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        // operation worksheet management
        if($prevdate) {
            $prevOpsDate = Operationdate::where('person_id', $transaction->person->id)->whereDate('delivery_date', '=', $prevdate)->first();
        }else {
            $prevOpsDate = Operationdate::where('person_id', $transaction->person->id)->whereDate('delivery_date', '=', $transaction->delivery_date)->first();
        }

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

    private function removeOperationDates($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        // operation worksheet management
        $opsdate = Operationdate::where('person_id', $transaction->person->id)->where('delivery_date', $transaction->delivery_date)->where('color', 'Red')->first();

        if($opsdate) {
            $opsdate->delete();
        }
    }
}