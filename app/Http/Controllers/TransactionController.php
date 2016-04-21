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
                        ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name', 'transactions.created_at', 'profiles.gst')
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

        if($request->input('save')){

            $request->merge(array('status' => 'Pending'));

        }elseif($request->input('del_paid')){

            $request->merge(array('status' => 'Delivered'));

            $request->merge(array('pay_status' => 'Paid'));

            $request->merge(['paid_by' => Auth::user()->name]);

            $request->merge(array('driver'=>Auth::user()->name));

        }elseif($request->input('del_owe')){

            $request->merge(array('status' => 'Delivered'));

            $request->merge(array('pay_status' => 'Owe'));

            $request->merge(array('driver'=>Auth::user()->name));

        }elseif($request->input('paid')){

            $request->merge(array('pay_status' => 'Paid'));

            $request->merge(array('paid_by' => Auth::user()->name));

        }elseif($request->input('confirm')){

            $request->merge(array('status' => 'Confirmed'));

        }elseif($request->input('unpaid')){

            $request->merge(array('pay_status' => 'Owe'));

            $request->merge(array('paid_by' => null));
        }

        $transaction = Transaction::findOrFail($id);

        $request->merge(array('person_id' => $request->input('person_copyid')));

        $request->merge(array('updated_by' => Auth::user()->name));

        $transaction->update($request->all());

        if($transaction->status === 'Confirmed' or $transaction->status === 'Delivered' or $transaction->status === 'Verified Owe' or $transaction->status === 'Verified Paid'){

            if($quantities and $amounts){

                if($this->createDeal($transaction, $quantities, $amounts, $quotes)){

                    $this->syncDealInv($transaction);

                    Flash::success('Successfully Added');

                }else{

                    $this->syncDealInv($transaction);

                    Flash::error('Stock Qty is not enough');

                    return Redirect::action('TransactionController@edit', $transaction->id);

                }

            }else{

                if($this->syncDealInv($transaction)){

                    Flash::success('Successfully Added');

                }else{

                    Flash::error('Stock Qty is not enough');

                    return Redirect::action('TransactionController@edit', $transaction->id);
                }
            }

        }elseif($transaction->status === 'Pending'){

            if($quantities and $amounts){

                if($this->createDeal($transaction, $quantities, $amounts, $quotes)){

                    Flash::success('Successfully Added');

                }else{

                    Flash::error('Stock Qty is not enough');

                    return Redirect::action('TransactionController@edit', $transaction->id);

                }
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

            $transaction->save();

            // revert all the deals qty upon cancelled
            $deals = Deal::where('transaction_id', $transaction->id)->where('qty_status', 'deducted')->get();

            foreach($deals as $deal){

                $item = Item::findOrfail($deal->item_id);

                $item->qty_last = $item->qty_now;

                $item->qty_now = $item->qty_now + $deal->qty;

                $item->save();

                $deal->qty_status = 'removed';

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

        return redirect('transaction');

    }

    public function showPersonTransac($person_id)
    {
        return Transaction::with('person')->wherePersonId($person_id)->latest()->take(5)->get();
    }

    public function reverse($id)
    {
        $transaction = Transaction::findOrFail($id);

        if($transaction->cancel_trace){

            $transaction->status = $transaction->cancel_trace;

            $transaction->updated_by = Auth::user()->name;

        }else{
            // this will affect inventories in later days
            $transaction->status = 'Pending';

            $transaction->updated_by = Auth::user()->name;
        }


        $transaction->save();

        return Redirect::action('TransactionController@edit', $transaction->id);
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

    private function createDeal($transaction, $quantities, $amounts, $quotes)
    {
        foreach($quantities as $index => $qty){

            if($qty != NULL or $qty != 0 ){

                $deal = new Deal();

                $deal->transaction_id = $transaction->id;

                $deal->item_id = $index;

                $deal->qty = $qty;

                // deduct inventory
                $item = Item::findOrFail($index);

                // email notification for stock running low
                if($item->email_limit){

                    if($item->qty_now - $qty < $item->email_limit){

                        $this->sendEmailAlert($item);

                    }
                }

                // restrict picking negative
                if($item->lowest_limit){

                    if($item->qty_now - $qty < $item->lowest_limit){

                        return false;

                    }

                }else{

                    if($item->qty_now - $qty < 0){

                        return false;

                    }
                }

                $deal->amount = $amounts[$index];

                $deal->unit_price = $quotes[$index];

                $deal->save();

            }
        }

        $deals = Deal::whereTransactionId($transaction->id)->get();

        $deal_total = $deals->sum('amount');

        $deal_totalqty = $deals->sum('qty');

        $transaction->total = $deal_total;

        $transaction->total_qty = $deal_totalqty;

        $transaction->save();

        return true;

    }

    private function syncDealInv($transaction)
    {

        // Looping to update the inventory
        if($transaction->status === 'Confirmed' or $transaction->status === 'Delivered' or $transaction->status === 'Verified Owe' or $transaction->status === 'Verified Paid'){

            $deals = Deal::where('transaction_id', $transaction->id)->whereNull('qty_status')->get();

            foreach($deals as $deal){

                $item = Item::findOrFail($deal->item_id);

                // email notification for stock running low
                if($item->email_limit){

                    if($item->qty_now - $deal->qty < $item->email_limit){

                        $this->sendEmailAlert($item);

                    }
                }

                // restrict picking negative
                if($item->lowest_limit){

                    if($item->qty_now - $deal->qty < $item->lowest_limit){

                        return false;

                    }

                }else{

                    if($item->qty_now - $deal->qty < 0){

                        return false;

                    }
                }

                $item->qty_now = $item->qty_now - $deal->qty;

                $deal->qty_status = 'deducted';

                $item->save();

                $deal->save();
            }

            return true;

        }else{

            return true;

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
