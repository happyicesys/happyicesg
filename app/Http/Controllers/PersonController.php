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
use DB;

class PersonController extends Controller
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

    /**
     * Return viewing page.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('person.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('person.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PersonRequest $request)
    {
        $input = $request->all();

        $person = Person::create($input);

        return redirect('person');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $person = Person::findOrFail($id);

        $files = StoreFile::wherePersonId($id)->latest()->get();

        $prices = Price::wherePersonId($id)->oldest()->paginate(50);

        $addfreezers = AddFreezer::wherePersonId($id)->oldest()->paginate(3);

        $addaccessories = AddAccessory::wherePersonId($id)->oldest()->paginate(3);

        return view('person.edit', compact('person', 'files', 'prices', 'addfreezers', 'addaccessories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $person = Person::findOrFail($id);

        $files = StoreFile::wherePersonId($id)->oldest()->get();

        $prices = Price::wherePersonId($id)->orderBy('item_id')->paginate(50);

        $addfreezers = AddFreezer::wherePersonId($id)->oldest()->paginate(3);

        $addaccessories = AddAccessory::wherePersonId($id)->oldest()->paginate(3);

        return view('person.edit', compact('person', 'files', 'prices', 'addfreezers', 'addaccessories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

        // $this->syncFreezer($person, $request);

        // $this->syncAccessory($person, $request);

        return Redirect::action('PersonController@edit', $person->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = Person::findOrFail($id);

        $person->delete();

        return redirect('person');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return json
     */
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

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
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

        // using sql query instead of eloquent for super fast pre-load (api)
        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->select('transactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id', 'transactions.status', 'transactions.delivery_date', 'transactions.driver', 'transactions.total', 'transactions.total_qty', 'transactions.pay_status', 'transactions.updated_by', 'transactions.updated_at', 'profiles.name', 'transactions.created_at', 'profiles.gst')
                        ->where('people.id', '=', $person_id)
                        ->latest('created_at')
                        ->get();

        return $transactions;
    }

    public function generateLogs($id)
    {
        $person = Person::findOrFail($id);

        $personHistory = $person->revisionHistory;

        // $prices = Price::wherePersonId($id)->get();

       // foreach($prices as $price){

        // $priceHistory = $prices->revisionHistory;

       // }


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
        $prices = Price::wherePersonId($person_id)->get();

        return $prices;
    }

    public function storeNote($person_id, Request $request)
    {
        $person = Person::findOrFail($person_id);

        $person->note = $request->note;

        $person->save();

        return Redirect::action('PersonController@edit', $person->id);
    }

}
