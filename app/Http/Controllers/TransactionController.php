<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Response;
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

class TransactionController extends Controller
{

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getData()
    {
        $transactions =  Transaction::with(['person', 'user'])->get();

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

        $prices = Price::wherePersonId($transaction->person_id)->whereNotNull('retail_price')->get();

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

        }

        $transaction = Transaction::findOrFail($id);

        $request->merge(array('person_id' => $request->input('person_copyid')));

        $request->merge(array('updated_by' => Auth::user()->name));

        $transaction->update($request->all());

        $this->createDeal($transaction->id, $quantities, $amounts);

        if($request->input('save')){

            return redirect('transaction');

        }else{

            return Redirect::action('TransactionController@edit', $transaction->id);

        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->delete();

        return redirect('transaction');
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

            $query->where('person_id', $person_id)->whereNotNull('retail_price');

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

    public function generateInvoice($id)    
    {

        $transaction = Transaction::findOrFail($id);

        $person = Person::findOrFail($transaction->person_id);

        $deals = Deal::whereTransactionId($transaction->id)->get();

        $totalprice = DB::table('deals')->whereTransactionId($transaction->id)->sum('amount');

        $profile = Profile::firstOrFail();

        $data = [
            'transaction'   =>  $transaction,
            'person'        =>  $person,
            'deals'         =>  $deals,
            'totalprice'    =>  $totalprice,
            'profile'       =>  $profile,
        ];

        $name = 'Inv('.$transaction->id.')_'.Carbon::now()->format('dmYHis').'.pdf';

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

    private function createDeal($id, $quantities, $amounts)
    {
        foreach($quantities as $index => $qty){

            if($qty != NULL or $qty != 0 ){

                $deal = new Deal();

                $deal->transaction_id = $id;

                $deal->item_id = $index;

                $deal->qty = $qty;

                $deal->amount = $amounts[$index];

                $deal->save();

            }
        }        

    } 

     
}
