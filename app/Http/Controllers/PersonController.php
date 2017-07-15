<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Person;
use Carbon\Carbon;
use App\StoreFile;
use App\Price;
use App\Transaction;
use App\Freezer;
use App\Accessory;
use App\Profile;
use App\AddFreezer;
use App\AddAccessory;
use App\Deal;
use Auth;
use DB;

class PersonController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getPersonData($person_id)
    {
        $person =  Person::findOrFail($person_id);
        return $person;
    }

    public function getData()
    {
        $person =  Person::with('custcategory')->where(function($query){
            $query->where('cust_id', 'NOT LIKE', 'H%');
        })->orderBy('cust_id')->get();
        return $person;
    }

    // retrieve api data list for the person index (Formrequest $request)
    public function getPeopleApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $people = DB::table('people')
                        ->leftJoin('custcategories', 'people.custcategory_id', '=', 'custcategories.id')
                        ->select(
                                    'people.id', 'people.cust_id', 'people.company', 'people.name', 'people.contact',
                                    'people.alt_contact', 'people.del_address', 'people.del_postcode', 'people.active',
                                    'people.payterm',
                                    'custcategories.name as custcategory'
                                );

        // reading whether search input is filled
        if($request->cust_id or $request->custcategory or $request->company or $request->contact or $request->active) {
            $people = $this->searchPeopleDBFilter($people, $request);
        }else {
            if($request->sortName) {
                $people = $people->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
            }
        }

        // condition (exclude all H code)
        $people = $people->where('people.cust_id', 'NOT LIKE', 'H%');

        if($pageNum == 'All'){
            $people = $people->latest('people.created_at')->get();
        }else{
            $people = $people->latest('people.created_at')->paginate($pageNum);
        }

        $data = [
            'people' => $people,
        ];

        return $data;
    }

    public function getPersonUserId($user_id)
    {
        $person = Person::where('user_id', $user_id)->first();
        return $person;
    }

    public function index()
    {
        return view('person.index');
    }

    public function create()
    {
        return view('person.create');
    }

    public function store(PersonRequest $request)
    {
        $input = $request->all();
        $person = Person::create($input);
        $person->is_vending = $request->has('is_vending')? 1 : 0;
        $person->save();
        return Redirect::action('PersonController@edit', $person->id);
    }

    public function show($id)
    {
        $person = Person::findOrFail($id);
        $files = StoreFile::wherePersonId($id)->latest()->get();
        $prices = Price::wherePersonId($id)->oldest()->paginate(50);
        $addfreezers = AddFreezer::wherePersonId($id)->oldest()->paginate(3);
        $addaccessories = AddAccessory::wherePersonId($id)->oldest()->paginate(3);
        return view('person.edit', compact('person', 'files', 'prices', 'addfreezers', 'addaccessories'));
    }

    public function edit($id)
    {
        $person = Person::findOrFail($id);
        $files = StoreFile::wherePersonId($id)->oldest()->get();
        $prices = Price::wherePersonId($id)->orderBy('item_id')->paginate(50);
        $addfreezers = AddFreezer::wherePersonId($id)->oldest()->paginate(3);
        $addaccessories = AddAccessory::wherePersonId($id)->oldest()->paginate(3);
        return view('person.edit', compact('person', 'files', 'prices', 'addfreezers', 'addaccessories'));
    }

    public function update(PersonRequest $request, $id)
    {
        $person = Person::findOrFail($id);
        if($request->input('active')){
            if($person->active == 'Yes'){
                $request->merge(array('active' => 'No'));
            }else{
                $request->merge(array('active' => 'Yes'));
            }
        }
        $input = $request->all();
        $person->update($input);
        $person->is_vending = $request->has('is_vending') ? 1 : 0;
        $person->save();
        if(! $person->is_vending) {
            $person->vending_piece_price = 0.00;
            $person->vending_monthly_rental = 0.00;
            $person->vending_profit_sharing = 0.00;
            $person->save();
        }
        return Redirect::action('PersonController@edit', $person->id);
    }

    public function destroy($id)
    {
        $person = Person::findOrFail($id);
        $person->delete();
        return redirect('person');
    }

    public function destroyAjax($id)
    {
        $person = Person::findOrFail($id);
        $person->delete();
        return $person->name . 'has been successfully deleted';
    }

    public function addFile(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        $file = $request->file('file');
        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
        $file->move('person_asset/file', $name);
        $person->files()->create(['path' => "/person_asset/file/{$name}"]);

    }

    public function removeFile($id)
    {
        $file = StoreFile::findOrFail($id);
        $filename = $file->path;
        $path = public_path();
        if (!File::delete($path.$filename))
        {
            $file->delete();
            return Redirect::action('PersonController@edit', $file->person_id);
        }else {
            $file->delete();
            return Redirect::action('PersonController@edit', $file->person_id);
        }
    }

    public function showTransac(Request $request, $person_id)
    {
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;
        $person = Person::findOrFail($person_id);
/*        if($person->cust_id[0] === 'H') {
            $transactions1 = DB::table('dtdtransactions')
                            ->leftJoin('people', 'dtdtransactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->select(
                                DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN dtdtransactions.delivery_fee>0 THEN dtdtransactions.total*107/100 + dtdtransactions.delivery_fee ELSE dtdtransactions.total*107/100 END) ELSE (CASE WHEN dtdtransactions.delivery_fee>0 THEN dtdtransactions.total + dtdtransactions.delivery_fee ELSE dtdtransactions.total END) END, 2) AS total'),
                                    'dtdtransactions.delivery_fee','dtdtransactions.id AS id', 'dtdtransactions.status AS status',
                                    'dtdtransactions.delivery_date AS delivery_date', 'dtdtransactions.driver AS driver',
                                    'dtdtransactions.total_qty AS total_qty', 'dtdtransactions.pay_status AS pay_status',
                                    'dtdtransactions.updated_by AS updated_by', 'dtdtransactions.updated_at AS updated_at',
                                    'dtdtransactions.created_at AS created_at', 'dtdtransactions.pay_method',
                                    'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id',
                                    'profiles.name', 'profiles.gst'
                                )
                            ->where('people.id', '=', $person_id);

            $transactions2 = DB::table('transactions')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->select(
                                DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN transactions.total*107/100 + transactions.delivery_fee ELSE transactions.total*107/100 END) ELSE (CASE WHEN transactions.delivery_fee>0 THEN transactions.total + transactions.delivery_fee ELSE transactions.total END) END, 2) AS total'),
                                    'transactions.delivery_fee','transactions.id AS id', 'transactions.status AS status',
                                    'transactions.delivery_date AS delivery_date', 'transactions.driver AS driver',
                                    'transactions.total_qty AS total_qty', 'transactions.pay_status AS pay_status',
                                    'transactions.updated_by AS updated_by', 'transactions.updated_at AS updated_at',
                                    'transactions.created_at AS created_at', 'transactions.pay_method',
                                    'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id',
                                    'profiles.name', 'profiles.gst'
                                )
                            ->where('people.id', '=', $person_id);

            $transactions = $transactions1
                            ->union($transactions2);
        }else{*/
            $transactions = DB::table('transactions')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->select(
                                DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN transactions.total*107/100 + transactions.delivery_fee ELSE transactions.total*107/100 END) ELSE (CASE WHEN transactions.delivery_fee>0 THEN transactions.total + transactions.delivery_fee ELSE transactions.total END) END, 2) AS total'),
                                    'transactions.delivery_fee','transactions.id AS id', 'transactions.status AS status',
                                    'transactions.delivery_date AS delivery_date', 'transactions.driver AS driver',
                                    'transactions.total_qty AS total_qty', 'transactions.pay_status AS pay_status',
                                    'transactions.updated_by AS updated_by', 'transactions.updated_at AS updated_at',
                                    'transactions.created_at AS created_at', 'transactions.pay_method',
                                    'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id',
                                    'profiles.name', 'profiles.gst'
                                )
                            ->where('people.id', '=', $person_id);
        // }

        // reading whether search input is filled
        if($request->id or $request->status or $request->pay_status or $request->delivery_from or $request->delivery_to or $request->driver){
            $transactions = $this->searchTransactionDBFilter($transactions, $request);
        }else{
            if($request->sortName){
                $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
            }
        }

        $totals = $this->calTotals($transactions);

        if($pageNum == 'All'){
            $transactions = $transactions->latest('created_at')->get();
        }else{
            $transactions = $transactions->latest('created_at')->paginate($pageNum);
        }

        $profileTransactionsId = [];
        foreach(Transaction::wherePersonId($person->id)->get() as $transac) {
            array_push($profileTransactionsId, $transac->id);
        }
        $profileDealsGrossProfit = number_format((Deal::whereIn('transaction_id', $profileTransactionsId)->sum('amount') - Deal::whereIn('transaction_id', $profileTransactionsId)->sum(DB::raw('qty * unit_cost'))), 2, '.', '');

        $data = [
            'total_amount' => $totals['total_amount'],
            'total_paid' => $totals['total_paid'],
            'total_owe' => $totals['total_owe'],
            'profileDealsGrossProfit' => $profileDealsGrossProfit,
            'transactions' => $transactions,
        ];

        return $data;
    }

    public function generateLogs($id)
    {
        $person = Person::findOrFail($id);
        $personHistory = $person->revisionHistory;
        return view('person.log', compact('person', 'personHistory'));
    }

    public function getProfile($person_id)
    {
        $person = Person::findOrFail($person_id);
        $profile = Profile::findOrFail($person->profile_id);
        return $profile;
    }

    public function addFreezer(Request $request)
    {
        $this->validate($request, [
                'freezer_id',
            ]);
        $addfreezer = AddFreezer::create($request->all());
        return Redirect::action('PersonController@edit', $addfreezer->person_id);
    }

    public function removeFreezer($id)
    {
        $addfreezer = AddFreezer::findOrFail($id);
        $addfreezer->delete();
        return Redirect::action('PersonController@edit', $addfreezer->person_id);
    }

    public function addAccessory(Request $request)
    {
        $this->validate($request, [
                'accessory_id',
            ]);
        $addaccessory = AddAccessory::create($request->all());
        return Redirect::action('PersonController@edit', $addaccessory->person_id);
    }

    public function removeAccessory($id)
    {
        $addaccessory = AddAccessory::findOrFail($id);
        $addaccessory->delete();
        return Redirect::action('PersonController@edit', $addaccessory->person_id);
    }

    public function personPrice($person_id)
    {
        $person = Person::findOrFail($person_id);
        if($person->cust_id[0] === 'H') {
            $prices = Price::wherePersonId(1643)->get();
        }else{
            $prices = Price::wherePersonId($person_id)->get();
        }
        return $prices;
    }

    public function storeNote($person_id, Request $request)
    {
        $person = Person::findOrFail($person_id);
        $person->note = $request->note;
        $person->save();
        return Redirect::action('PersonController@edit', $person->id);
    }

    // return dtd members api for select format
    public function getMemberSelectApi()
    {
        $members = '';
        $member = Person::whereUserId(Auth::user()->id)->first();
        $admin = Auth::user()->hasRole('admin');
        if($admin) {
            // $members = Person::where('cust_id', 'LIKE', 'D%')->orderBy('cust_id', 'asc')->pluck('name')->all();
            $members = Person::where('cust_id', 'LIKE', 'D%')->orderBy('cust_id', 'asc')->get();
        }else if($member and !$admin) {
            // $members = $member->descendantsAndSelf()->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_id', 'asc')->pluck('name')->all();
            $members = $member->descendantsAndSelf()->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_id', 'asc')->get();
        }
        // return [''] + $members;
        return $members;
    }

    // replicate the person particulars(int $person_id)
    public function replicatePerson($person_id)
    {
        $person = Person::findOrFail($person_id);
        $rep_person = $person->replicate();
        $find_already_replicate = Person::where('cust_id', 'LIKE', $person->cust_id.'-replicate-%');
        $rep_person->cust_id = $find_already_replicate->first() ?  substr($find_already_replicate->max('cust_id'), 0, -1).(substr($find_already_replicate->max('cust_id'), -1) + 1) : $person->cust_id.'-replicate-1';
        $rep_person->save();

        // replicate pricelist
        $prices = $person->prices;
        foreach($prices as $price) {
            $rep_price = new Price();
            $rep_price->retail_price = $price->retail_price;
            $rep_price->quote_price = $price->quote_price;
            $rep_price->remark = $price->remark;
            $rep_price->person_id = $rep_person->id;
            $rep_price->item_id = $price->item_id;
            $rep_price->save();
        }
        return Redirect::action('PersonController@edit', $rep_person->id);
    }

    // conditional filter parser(Collection $query, Formrequest $request)
    private function searchPeopleDBFilter($people, $request)
    {
        $cust_id = $request->cust_id;
        $custcategory = $request->custcategory;
        $company = $request->company;
        $contact = $request->contact;
        $active = $request->active;

        if($cust_id){
            $people = $people->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($custcategory){
            $people = $people->where('custcategories.id', $custcategory);
        }
        if($company){
            $people = $people->where('people.company', 'LIKE', '%'.$company.'%');
        }
        if($contact){
            $people = $people->where(function($query) use ($contact) {
                $query->where('people.contact', 'LIKE', '%'.$contact.'%')->orWhere('people.alt_contact', 'LIKE', '%'.$contact.'%');
            });
        }
        if($active){
            $people = $people->where('people.active', 'LIKE', '%'.$active.'%');
        }
        return $people;
    }

    // conditional filter parser for transactions(Collection $query, Formrequest $request)
    private function searchTransactionDBFilter($transactions, $request)
    {
        $id = $request->id;
        $status = $request->status;
        $pay_status = $request->pay_status;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;
        $driver = $request->driver;

        if($id){
            $transactions = $transactions->where('id', 'LIKE', '%'.$id.'%');
        }
        if($status){
            $transactions = $transactions->where('status', 'LIKE', '%'.$status.'%');
        }
        if($pay_status){
            $transactions = $transactions->where('pay_status', 'LIKE', '%'.$pay_status.'%');
        }
        if($delivery_from){
            $transactions = $transactions->where('delivery_date', '>=', $delivery_from);
        }
        if($delivery_to){
            $transactions = $transactions->where('delivery_date', '<=', $delivery_to);
        }
        if($driver){
            $transactions = $transactions->where('driver', 'LIKE', '%'.$driver.'%');
        }
        return $transactions;
    }

    // calculate transactions totals (Collection $transactiions)
    private function calTotals($query)
    {
        $total_amount = 0;
        $total_paid = 0;
        $total_owe = 0;

        $query1 = clone $query;
        $query2 = clone $query;
        $query3 = clone $query;
/*
        $transactions = $query1->get();

        foreach($transactions as $transaction) {
            $amount = 0;

            if($transaction->gst === 1) {
                $amount += ($transaction->total * 107/100);
            }else {
                $amount += $transaction->total;
            }

            if($transaction->delivery_fee > 0) {
                $amount += $transaction->delivery_fee;
            }

            switch($transaction->pay_status) {
                case 'Paid':
                    $total_paid += $amount;
                    break;
                case 'Owe':
                    $total_owe += $amount;
                    break;
            }
        }

        $total_amount = $total_paid + $total_owe;*/


        $total_amount = $query1->sum(DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN transactions.total*107/100 + transactions.delivery_fee ELSE transactions.total*107/100 END) ELSE (CASE WHEN transactions.delivery_fee>0 THEN transactions.total + transactions.delivery_fee ELSE transactions.total END) END, 2)'));
        $total_paid = $query2->where('transactions.pay_status', 'Paid')->sum(DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN transactions.total*107/100 + transactions.delivery_fee ELSE transactions.total*107/100 END) ELSE (CASE WHEN transactions.delivery_fee>0 THEN transactions.total + transactions.delivery_fee ELSE transactions.total END) END, 2)'));
        $total_owe = $query3->where('transactions.pay_status', 'Owe')->sum(DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN transactions.total*107/100 + transactions.delivery_fee ELSE transactions.total*107/100 END) ELSE (CASE WHEN transactions.delivery_fee>0 THEN transactions.total + transactions.delivery_fee ELSE transactions.total END) END, 2)'));
        $totals = [
            'total_amount' => $total_amount,
            'total_paid' => $total_paid,
            'total_owe' => $total_owe
        ];
        return $totals;
    }

}
