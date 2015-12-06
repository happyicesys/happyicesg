<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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

       /* $sortBy = $request->get('sortBy');

        $direction = $request->get('direction');

        if($sortBy and $direction)
        {
            if($sortBy == 'company'){

                $transactions = Transaction::with(['people' => function($query){

                    $query->orderBy($sortBy, $direction);

                }])->Paginate(10); 
            
            }else{

               $transactions = Transaction::orderBy($sortBy, $direction)->Paginate(10); 

            }


        }else{
            
            $transactions = Transaction::Paginate(10);

        }*/

        // dd($transactions->first());

        // return view('transaction.index', compact('transactions'));
        return view('transaction.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->merge(array('status' => 'Pending'));

        $input = $request->all();

        $transaction = new Transaction($input);

        Auth::user()->transactions()->save($transaction);

        return Redirect::action('TransactionController@edit', $transaction->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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

        return view('transaction.edit', compact('transaction'));
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

        if($request->input('save')){

            $request->merge(array('status' => 'Pending'));

        }else{

            $request->merge(array('status' => 'Confirmed'));

        }

        $transaction = Transaction::findOrFail($id);

        $request->merge(array('person_id' => $request->input('person_copyid')));
        // dd($request->input('person_id'));
        // dd($request->input('person_copyid'));

        $transaction->update($request->all());

        if($request->input('conprint') or $request->input('print')){

            $this->generateInvoice($transaction->id);

        }elseif($request->input('save')){

            return redirect('transaction');

        }

        return Redirect::action('TransactionController@edit', $transaction->id);
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
        return Item::whereHas('prices', function($query) use ($person_id){

            $query->where('person_id', $person_id);

        })->get();
        //select(DB::raw("CONCAT(product_id,' - ',name,' - ',remark) AS full, id"))->lists('full', 'id');

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

    private function syncTransaction(Request $request)
    {
        // dd(Auth::user()->toJson());
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

    private function generateInvoice($id)    
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

        $pdf = App::make('snappy.pdf');

        $html = view('transaction.invoice', $data)->render();

        $name = 'Inv('.$transaction->id.')_'.Carbon::now()->format('dmYHi').'.pdf';

        $path = public_path().'/person_asset/invoice/'.$person->cust_id.'/'.$name;

        // $pdf->setOption('footer-html', 

/*        $pdf->generateFromHtml($html, $path,[
                'page-height' => null,
                'page-width'  => null,
                'dpi' => 300,
                'image-dpi' => 300, 
                'lowquality' => false,              
            ],$overwrite = true);*/

        $pdf->getOutputFromHtml($html,[
                'page-height' => null,
                'page-width'  => null,
                'dpi' => 300,
                'image-dpi' => 300, 
                'lowquality' => false,              
            ]); 

return $pdf->stream();
// return $pdf->download('invoice.pdf');                   

        // return $pdf->download();

       // $pdf = PDF::loadView('transaction.invoice', $transaction);

        // return $pdf->getOutput('invoice.pdf');
/*$snappy = App::make('snappy.pdf');
$snappy->generateFromHtml('<h1>Bill</h1><p>You owe me money, dude.</p>', '/tmp/bill-123.pdf');*/

/*$pdf = PDF::loadView('transaction.invoice', $transaction);
return $pdf->getOutput('/tmp/invoice.pdf');*/


    }     
}
