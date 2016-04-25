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

        $request->merge(['delivery_date' => Carbon::now()]);

        $request->merge(['order_date' => Carbon::now()]);

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
        $prices = DB::table('prices')
                    ->leftJoin('items', 'prices.item_id', '=', 'items.id')
                    ->select('prices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id')
                    ->where('prices.person_id', '=', $transaction->person_id)
                    ->orderBy('product_id')
                    ->get();

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

            $request->merge(['paid_by' => Auth::user()->name]);

            $request->merge(array('driver'=>Auth::user()->name));

            if(count($deals) == 0){

                Flash::error('Please entry the list');

                return Redirect::action('TransactionController@edit', $transaction->id);

            }

        }elseif($request->input('del_owe')){

            $request->merge(array('status' => 'Delivered'));

            $request->merge(array('pay_status' => 'Owe'));

            $request->merge(array('driver'=>Auth::user()->name));

            if(count($deals) == 0){

                Flash::error('Please entry the list');

                return Redirect::action('TransactionController@edit', $transaction->id);

            }

        }elseif($request->input('paid')){

            $request->merge(array('pay_status' => 'Paid'));

            $request->merge(array('paid_by' => Auth::user()->name));

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
        }


        $request->merge(array('person_id' => $request->input('person_copyid')));

        $request->merge(array('updated_by' => Auth::user()->name));

        $transaction->update($request->all());


        //Qty insert to on order upon confirmed(1) transaction status start
        if($transaction->status === 'Confirmed'){

            $this->syncDeal($transaction, $quantities, $amounts, $quotes, 1);

        }else if($transaction->status === 'Delivered' or $transaction->status === 'Verified Owe' or $transaction->status === 'Verified Paid'){

            $this->syncDeal($transaction, $quantities, $amounts, $quotes, 2);

        }
        //Qty insert to on order upon confirmed(1) transaction status end

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

            // revert all the deals qty upon cancelled
            $deals = Deal::where('transaction_id', $transaction->id)->whereIn('qty_status', ['1', '2'])->get();

            foreach($deals as $deal){

                $item = Item::findOrfail($deal->item_id);

                if($deal->qty_status == 1){

                    $item->qty_order -= $deal->qty;

                    $deal->qty_status = 3;

                }else if($deal->qty_status == 2){

                    $item->qty_last = $item->qty_now;

                    $item->qty_now += $deal->qty;

                    $deal->qty_status = 3;
                }

                $item->save();

                $deal->save();
            }

            return Redirect::action('TransactionController@edit', $transaction->id);

        }else{

            $transaction = Transaction::findOrFail($id);

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

        // $name = 'Inv('.$transaction->id.')_'.Carbon::now()->format('dmYHis').'.pdf';
        $name = 'Inv('.$transaction->id.')_'.$person->cust_id.'_'.$person->company.'.pdf';

        $pdf = PDF::loadView('transaction.invoice', $data);

        $pdf->setPaper('a4');

        return $pdf->download($name);

    }

    public function generateLogs($id)
    {
        $transaction = Transaction::findOrFail($id);

        // $transaction = $transaction->with('deals')


        $transHistory = $transaction->revisionHistory;

        // dd($transHistory->toJson());

        /*$revisionDeal = Revision::whereRevisionableType('App\Deal')->with(array('deals' => function($query) use ($id){
                            $query->where('transaction_id', $id);
                        }))->get();
        $revisions = Revision::all();
        dd($revisionDeal->toJson());*/

        return view('transaction.log', compact('transaction', 'transHistory'));
    }

    public function searchDateRange(Request $request)
    {
        $request->input('property');

        $request->input('startDate');

        $request->input('endDate');

    }

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

            $transaction->updated_by = Auth::user()->name;

            $transaction->save();
        }

        // return redirect('transaction');
        return redirect()->back();

    }

    public function showPersonTransac($person_id)
    {
        return Transaction::with('person')->wherePersonId($person_id)->latest()->take(5)->get();
    }

    public function reverse($id)
    {
        $transaction = Transaction::findOrFail($id);

        $deals = Deal::where('transaction_id', $transaction->id)->where('qty_status', '3')->get();

        if($transaction->cancel_trace){

            if($transaction->cancel_trace === 'Confirmed'){

                foreach($deals as $deal){

                    $item = Item::findOrFail($deal->item_id);

                    $item->qty_order += $deal->qty;

                    $deal->qty_status = 1;

                    $item->save();

                    $deal->save();

                }

            }else if($transaction->cancel_trace === 'Delivered' or $transaction->cancel_trace === 'Verified Owe' or $transaction->cancel_trace === 'Verified Paid'){

                foreach($deals as $deal){

                    $item = Item::findOrFail($deal->item_id);

                    $item->qty_now -= $deal->qty;

                    $deal->qty_status = 2;

                    $item->save();

                    $deal->save();

                }

            }
            $transaction->status = $transaction->cancel_trace;

            $transaction->cancel_trace = '';

            $transaction->updated_by = Auth::user()->name;

        }else{
            // this will affect inventories in later days
            $transaction->status = 'Pending';

            $transaction->updated_by = Auth::user()->name;
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

    private function syncDeal($transaction, $quantities, $amounts, $quotes, $status)
    {
        if(array_filter($quantities) != null and array_filter($amounts) != null){

            $errors = array();

            foreach($quantities as $index => $qty){

                if($qty != NULL or $qty != 0 ){

                    // inventory lookup before saving to deals start
                    $item = Item::findOrFail($index);

                    // inventory email notification for stock running low start
                    if($item->email_limit){

                        if(($status == 1 and ($item->qty_now - $item->qty_order - $qty < $item->email_limit)) or ($status == 2 and ($item->qty_now - $qty < $item->email_limit))){

                            $this->sendEmailAlert($item);

                        }
                    }
                    // inventory email notification for stock running low end

                    // restrict picking negative stock & deduct/add actual/order if success start
                    if($status == 1){

                        if($item->qty_now - $item->qty_order - $qty < ($item->lowest_limit ? $item->lowest_limit : 0)){

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

                            $item->qty_order += $qty;

                            $item->save();

                        }

                    }else if($status == 2){

                        if($item->qty_now - $qty < ($item->lowest_limit ? $item->lowest_limit : 0)){

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

                        }
                    }
                    // restrict picking negative stock & deduct/add actual/order if success end
                }
            }
        }

        // if other than confirmed activated convert qty order to qty actual deduction start
        if($status == 2){

            $deal_actions = Deal::where('transaction_id', $transaction->id)->where('qty_status', '1')->get();

            foreach($deal_actions as $deal){

                $item = Item::findOrFail($deal->item_id);

                $item->qty_order -= $deal->qty;

                $item->qty_now -= $deal->qty;

                $item->save();

                $deal->qty_status = $status;

                $deal->save();
            }
        }
        // if other than confirmed activated convert qty order to qty actual deduction end


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

    private function sendEmailAlert($item)
    {

        $today = Carbon::now()->format('dmYHis');

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


}
