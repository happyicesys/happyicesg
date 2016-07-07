<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

use Venturecraft\Revisionable\Revision;
use Response;
use App;
use DB;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;
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

class TransactionController extends Controller
{
    //qty status condition
    /*
        qty_status = 1 (Stock Order/ Confirmed)
        qty_status = 2 (Actual Stock Deducted/ Delivered)
        qty_status = 3 (Stock Removed/ Deleted || Cancelled)
    */

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get transactions api data based on delivery date
    public function getData()
    {
        // using sql query instead of eloquent for super fast pre-load (api)
        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name', 'transactions.created_at', 'profiles.gst', 'transactions.pay_method', 'transactions.note')
                        ->latest('created_at')
                        ->get();
        return $transactions;
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

        $request->merge(array('updated_by' => Auth::user()->name));

        $request->merge(['delivery_date' => Carbon::today()]);

        $request->merge(['order_date' => Carbon::today()]);

        $input = $request->all();

        $transaction = Transaction::create($input);

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

        $person = Person::findOrFail($transaction->person_id);

        // retrieve manually to order product id asc
        if($transaction->person_code[0] === 'D'){

            $prices = DB::table('dtdprices')
                        ->leftJoin('items', 'dtdprices.item_id', '=', 'items.id')
                        ->select('dtdprices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id')
                        ->orderBy('product_id')
                        ->get();

        }else{

            $prices = DB::table('prices')
                        ->leftJoin('items', 'prices.item_id', '=', 'items.id')
                        ->select('prices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id')
                        ->where('prices.person_id', '=', $transaction->person_id)
                        ->orderBy('product_id')
                        ->get();
        }

        return view('transaction.edit', compact('transaction', 'person', 'prices'));
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

        // dynamic form arrays
        $quantities = $request->qty;

        $amounts = $request->amount;

        $quotes = $request->quote;

        $transaction = Transaction::findOrFail($id);

        // find out deals created
        $deals = Deal::where('transaction_id', $transaction->id)->get();

        if($request->input('save')){

            $request->merge(array('status' => 'Pending'));

        }elseif($request->input('del_paid')){

            $request->merge(array('status' => 'Delivered'));

            $request->merge(array('pay_status' => 'Paid'));

            if(! $request->paid_by){

                $request->merge(array('paid_by' => Auth::user()->name));
            }

            $request->merge(array('paid_at' => Carbon::now()->format('Y-m-d h:i A')));

            if(! $request->driver){

                $request->merge(array('driver'=>Auth::user()->name));
            }

            if(count($deals) == 0){

                Flash::error('Please entry the list');

                return Redirect::action('TransactionController@edit', $transaction->id);
            }

        }elseif($request->input('del_owe')){

            $request->merge(array('status' => 'Delivered'));

            $request->merge(array('pay_status' => 'Owe'));

            if(! $request->driver){

                $request->merge(array('driver'=>Auth::user()->name));
            }

            $request->merge(array('paid_by'=>null));

            if(count($deals) == 0){

                Flash::error('Please entry the list');

                return Redirect::action('TransactionController@edit', $transaction->id);
            }

        }elseif($request->input('paid')){

            $request->merge(array('pay_status' => 'Paid'));

            if(! $request->paid_by){

                $request->merge(array('paid_by' => Auth::user()->name));
            }

            $request->merge(array('paid_at' => Carbon::now()->format('Y-m-d h:i A')));

            if(count($deals) == 0){

                Flash::error('Please entry the list');

                return Redirect::action('TransactionController@edit', $transaction->id);

            }

        }elseif($request->input('confirm')){

            // confirmation must with the entries start
            if(array_filter($quantities) != null and array_filter($amounts) != null) {

                $request->merge(array('status' => 'Confirmed'));

            }else{

                Flash::error('The list cannot be empty upon confirmation');

                return Redirect::action('TransactionController@edit', $transaction->id);
            }
            // confirmation must with the entries end

        }elseif($request->input('unpaid')){

            $request->merge(array('pay_status' => 'Owe'));

            $request->merge(array('paid_by' => null));

            $request->merge(array('paid_at' => null));

        }elseif($request->input('update')){

            if($transaction->status === 'Confirmed'){

                $request->merge(array('driver' => null));

                $request->merge(array('paid_by' => null));

                $request->merge(array('paid_at' => null));

            }else if(($transaction->status === 'Delivered' or $transaction->status === 'Verified Owe') and $transaction->pay_status === 'Owe'){

                $request->merge(array('paid_by' => null));

                $request->merge(array('paid_at' => null));

            }
        }

        $request->merge(array('person_id' => $request->input('person_copyid')));

        $request->merge(array('updated_by' => Auth::user()->name));

        $transaction->update($request->all());

        //Qty insert to on order upon confirmed(1) transaction status
        if($transaction->status === 'Confirmed'){

            $this->syncDeal($transaction, $quantities, $amounts, $quotes, 1);

        }else if($transaction->status === 'Delivered' or $transaction->status === 'Verified Owe' or $transaction->status === 'Verified Paid'){

            $this->syncDeal($transaction, $quantities, $amounts, $quotes, 2);
        }

        $assign_cust = $transaction->person->cust_id;

        if($assign_cust[0] == 'D'){

            $this->syncOrder($transaction->id);
        }

        // update dtdtransaciton status to delivered
        if($request->input('del_owe') or $request->input('del_paid')){

            $this->dtdDelUpdate($transaction);
        }

        if($request->input('paid') or $request->input('del_paid')){

            $this->dtdPaidUpdate($transaction);
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

            $transaction->save();

            if($transaction->dtdtransaction_id){

                $dtdtransaction = DtdTransaction::findOrFail($transaction->dtdtransaction_id);

                $dtdtransaction->cancel_trace = $dtdtransaction->status;

                $dtdtransaction->status = 'Cancelled';

                $dtdtransaction->save();
            }

            $this->dealDeleteMultiple($transaction->id);

            return Redirect::action('TransactionController@edit', $transaction->id);

        }else{

            $transaction = Transaction::findOrFail($id);

            if($transaction->dtdtransaction_id){

                $dtdtransaction = DtdTransaction::where('id', $transaction->dtdtransaction_id)->first();

                if($dtdtransaction){

                    $dtdtransaction->status = 'Deleted';

                    $dtdtransaction->save();
                }
            }

            $transaction->delete();

            return redirect('transaction');
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

        $transaction = Transaction::findOrFail($id);

        $person = Person::findOrFail($transaction->person_id);

        $deals = Deal::whereTransactionId($transaction->id)->get();

        $totalprice = DB::table('deals')->whereTransactionId($transaction->id)->sum('amount');

        $totalqty = DB::table('deals')->whereTransactionId($transaction->id)->sum('qty');

        // $profile = Profile::firstOrFail();

        $data = [
            'transaction'   =>  $transaction,
            'person'        =>  $person,
            'deals'         =>  $deals,
            'totalprice'    =>  $totalprice,
            'totalqty'      =>  $totalqty,
            // 'profile'       =>  $profile,
        ];

        $name = 'Inv('.$transaction->id.')_'.$person->cust_id.'_'.$person->company.'.pdf';

        $pdf = PDF::loadView('transaction.invoice', $data);

        $pdf->setPaper('a4');

        return $pdf->download($name);
    }

    public function generateLogs($id)
    {
        $transaction = Transaction::findOrFail($id);

        $transHistory = $transaction->revisionHistory;

        return view('transaction.log', compact('transaction', 'transHistory'));
    }

    public function searchDateRange(Request $request)
    {
        $request->input('property');

        $request->input('startDate');

        $request->input('endDate');

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

            $transaction->pay_method = 'cash';

            $transaction->updated_by = Auth::user()->name;

            $transaction->save();
        }

        // using redirect back since applied in different views
        return redirect()->back();

    }

    public function showPersonTransac($person_id)
    {
        return Transaction::with('person')->wherePersonId($person_id)->latest()->take(5)->get();
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
    private function syncDeal($transaction, $quantities, $amounts, $quotes, $status)
    {
        if(array_filter($quantities) != null and array_filter($amounts) != null){

            // create array of errors to fetch errors from loop if any
            $errors = array();

            foreach($quantities as $index => $qty){

                if($qty != NULL or $qty != 0 ){

                    // inventory lookup before saving to deals
                    $item = Item::findOrFail($index);

                    // inventory email notification for stock running low
                    if($item->email_limit){

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

                    // restrict picking negative stock & deduct/add actual/order if success
                    if($status == 1){

                        if($this->calOrderLimit($qty, $item)){

                            array_push($errors, $item->product_id.' - '.$item->name);

                        }else{

                            $deal = new Deal();

                            $deal->transaction_id = $transaction->id;

                            $deal->item_id = $index;

                            $deal->qty = $qty;

                            $deal->amount = $amounts[$index];

                            $deal->unit_price = $quotes[$index];

                            $deal->qty_status = $status;

                            $deal->save();

                            $this->dealSyncOrder($index);

                        }

                    }else if($status == 2){

                        if($this->calActualLimit($qty, $item)){

                            array_push($errors, $item->product_id.' - '.$item->name);

                        }else{

                            $deal = new Deal();

                            $deal->transaction_id = $transaction->id;

                            $deal->item_id = $index;

                            $deal->qty = $qty;

                            $deal->amount = $amounts[$index];

                            $deal->unit_price = $quotes[$index];

                            $deal->qty_status = $status;

                            $deal->save();

                            $item->qty_now -= $qty;

                            $item->save();

                            $this->dealSyncOrder($index);

                        }
                    }
                }
            }
        }

        if($status == 2){

            $this->dealOrder2Actual($transaction->id);
        }


        $deals = Deal::whereTransactionId($transaction->id)->get();

        $deal_total = $deals->sum('amount');

        $deal_totalqty = $deals->sum('qty');

        $transaction->total = $deal_total;

        $transaction->total_qty = $deal_totalqty;

        $transaction->save();

        if(isset($errors)){

            if(count($errors) > 0){

                $errors_str = '';

                $errors_str = implode(" <br>", $errors);

                Flash::error('Stock Insufficient 缺货 (Please contact company 请联络公司): <br> '.$errors_str)->important();

            }

        }else{

            Flash::success('Successfully Added');
        }

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
        $deals = Deal::where('qty_status', '1')->where('item_id', $item_id);

        $item = Item::findOrFail($item_id);

        $item->qty_order = $deals->sum('qty');

        $item->save();

    }

    // convert order to actual deduction
    private function dealOrder2Actual($transaction_id)
    {
        $deals = Deal::where('qty_status', '1')->where('transaction_id', $transaction_id)->get();

        foreach($deals as $deal){

            $item = Item::findOrFail($deal->item_id);

            $deal->qty_status = 2;

            $item->qty_now -= $deal->qty;

            $deal->save();

            $item->save();

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

                $item->qty_now += $deal->qty;

                $deal->qty_status = 3;

                $deal->save();
            }

            $item->save();

            $this->dealSyncOrder($item->id);

        }
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

                $item->qty_now -= $deal->qty;

                $item->save();
            }
        }
    }

    private function calOrderLimit($qty, $item)
    {

        if(($item->qty_now - $item->qty_order - $qty < $item->lowest_limit ? $item->lowest_limit : 0) and ($qty > 0)){

            return true;

        }else{

            return false;
        }

    }

    private function calActualLimit($qty, $item)
    {

        if(($item->qty_now - $qty < $item->lowest_limit ? $item->lowest_limit : 0) and ($qty > 0)){

            return true;

        }else{

            return false;
        }

    }

    private function calOrderEmailLimit($qty, $item)
    {

        if(($item->qty_now - $item->qty_order - $qty < $item->email_limit) and ($qty > 0)){

            return true;

        }else{

            return false;
        }
    }

    private function calActualEmailLimit($qty, $item)
    {

        if(($item->qty_now - $qty < $item->email_limit) and ($qty > 0)){

            return true;

        }else{

            return false;
        }
    }

    private function syncOrder($transaction_id)
    {
        $dtdtransaction = DtdTransaction::where('transaction_id', $transaction_id)->first();

        $dtdtransaction->total = $dtdtransaction->total;

        $dtdtransaction->delivery_date = $dtdtransaction->delivery_date;

        $dtdtransaction->status = $dtdtransaction->status;

        $dtdtransaction->transremark = $dtdtransaction->transremark;

        $dtdtransaction->updated_by = $dtdtransaction->updated_by;

        $dtdtransaction->pay_status = $dtdtransaction->pay_status;

        $dtdtransaction->person_code = $dtdtransaction->person_code;

        $dtdtransaction->person_id = $dtdtransaction->person_id;

        $dtdtransaction->order_date = $dtdtransaction->order_date;

        $dtdtransaction->del_address = $dtdtransaction->del_address;

        $dtdtransaction->name = $dtdtransaction->name;

        $dtdtransaction->po_no = $dtdtransaction->po_no;

        $dtdtransaction->total_qty = $dtdtransaction->total_qty;

        $dtdtransaction->save();

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

                $dtddeal = new DtdDeal();

                $dtddeal->item_id = $dealresult->item_id;

                $dtddeal->transaction_id = $dtdtransaction->id;

                $dtddeal->qty = $dealresult->qty;

                $dtddeal->amount = $dealresult->amount;

                $dtddeal->unit_price = $dealresult->unit_price;

                $dtddeal->qty_status = $dealresult->qty_status;

                $dtddeal->deal_id = $dealresult->id;

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


}
