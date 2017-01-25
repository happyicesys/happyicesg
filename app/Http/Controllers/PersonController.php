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
        $person =  Person::where(function($query){
            $query->where('cust_id', 'NOT LIKE', 'H%');
        })->orderBy('cust_id')->get();
        return $person;
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
        return redirect('person');
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
        }
        else
        {
            $file->delete();
            return Redirect::action('PersonController@edit', $file->person_id);
        }
    }

    public function showTransac($person_id)
    {
        $person = Person::findOrFail($person_id);
        if($person->cust_id[0] === 'H') {
            $transactions1 = DB::table('dtdtransactions')
                            ->leftJoin('people', 'dtdtransactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->select('dtdtransactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id', 'dtdtransactions.status', 'dtdtransactions.delivery_date', 'dtdtransactions.driver', 'dtdtransactions.total', 'dtdtransactions.total_qty', 'dtdtransactions.pay_status', 'dtdtransactions.updated_by', 'dtdtransactions.updated_at', 'profiles.name', 'dtdtransactions.created_at', 'profiles.gst')
                            ->where('people.id', '=', $person_id);

            $transactions2 = DB::table('transactions')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name', 'transactions.created_at', 'profiles.gst')
                            ->where('people.id', '=', $person_id);
            $transactions = $transactions1->union($transactions2)->orderBy('created_at', 'desc')->get();
        }else{
            $transactions = DB::table('transactions')
                            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                            ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name', 'transactions.created_at', 'profiles.gst')
                            ->where('people.id', '=', $person_id)
                            ->latest('created_at')
                            ->get();
        }
        return $transactions;
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
}
