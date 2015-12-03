<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $sortBy = $request->get('sortBy');

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

        }

        // dd($transactions->first());

        return view('transaction.index', compact('transactions'));
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

        /*if($request->input('choice'))
        {
            $choice = $request->input('choice');

            if($choice == 1){
                
                $request->merge(array('person_id' => $request->input('person')));

                $this->syncTransaction($request);

            }elseif($choice == 2){

                $person = Person::create($request->all());

                $request->merge(array('person_id' => $person->id));

                $this->syncTransaction($request);

            }

        }

        return redirect('transaction');*/
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

        $profile = Profile::firstOrFail();

        $pdf = PDF::loadView('transaction.invoice', $transaction);

        return $pdf->download('invoice.pdf');
    }

    /*private function generateExcel($id)
    {
        $transaction = Transaction::findOrFail($id);

        $person = Person::findOrFail($transaction->person_id);
        // dd($person->bill_to);

        $deals = Deal::whereTransactionId($transaction->id)->get();

        $profile = Profile::firstOrFail();

        Excel::create('Invoice('.$transaction->id.')'.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($transaction, $person, $deals, $profile) {

            $excel->sheet('sheet1', function($sheet) use ($transaction, $person, $deals, $profile) {

                /*$sheet->setColumnFormat(array(
                    'A:P' => '@'
                ));

                //header
                $sheet->mergeCells('A2:I2');
                $sheet->mergeCells('A3:I3');
                $sheet->mergeCells('A4:I4');
                $sheet->mergeCells('A5:I5');

                $sheet->mergeCells('A8:B8');
                $sheet->mergeCells('A9:B9');
                $sheet->mergeCells('A10:B10');
                $sheet->mergeCells('A11:B11');
                
                
                $sheet->mergeCells('E8:F8');
                $sheet->mergeCells('C8:D8');
    
                //desc
                $sheet->mergeCells('B15:C15');
                $sheet->setWidth(array(
                    'A'     =>  20,
                    'B'     =>  20,
                    'C'     =>  15,
                    'D'     =>  10,
                    'E'     =>  10,
                    'F'     =>  10,
                    'G'     =>  15,
                    'H'     =>  15,
                    'I'     =>  15                                       
                ));

                $sheet->setPageMargin(0.15);

                // $sheet->setFontSize(15);

                // $sheet->setAllBorders('none');

                /*$sheet->setHeight(array(
                    1     =>  50,
                    2     =>  20,
                    3     =>  20,
                    4     =>  20,
                ));                           

                $sheet->loadView('transaction.invoice', compact('transaction', 'person', 'deals', 'profile'));

            });

        })->export('pdf');

        Flash::success('Reports successfully generated');

    } */      
}
