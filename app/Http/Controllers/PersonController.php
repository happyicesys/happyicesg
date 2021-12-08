<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\Person;
use Carbon\Carbon;
use App\StoreFile;
use App\Price;
use App\Transaction;
use App\Freezer;
use App\Accessory;
use App\Operationdate;
use App\Profile;
use App\AddFreezer;
use App\AddAccessory;
use App\Deal;
use App\User;
use App\Personmaintenance;
use App\OutletVisit;
use App\HasMonthOptions;
use Auth;
use DB;
use App\HasProfileAccess;
use App\Persontag;
use App\Persontagattach;
use App\Traits\HasCustcategoryAccess;

class PersonController extends Controller
{
    use HasProfileAccess, HasCustcategoryAccess, HasMonthOptions;

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getPersonData($person_id)
    {
        $person = Person::findOrFail($person_id);
        return $person;
    }

    public function getData()
    {
        $person = Person::with('custcategory')->where(function ($query) {
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

        $people = Person::with(['persontags', 'custcategory', 'profile', 'freezers', 'zone', 'accountManager'])
        ->leftJoin('custcategories', 'people.custcategory_id', '=', 'custcategories.id')
        ->leftJoin('custcategory_groups', 'custcategories.custcategory_group_id', '=', 'custcategory_groups.id')
        ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
        ->leftJoin('users AS account_managers', 'account_managers.id', '=', 'people.account_manager')
        ->leftJoin('zones', 'zones.id', '=', 'people.zone_id')
        ->leftJoin('users AS updater', 'updater.id', '=', 'people.updated_by')
        ->select(
            'people.id', 'people.cust_id', 'people.company', 'people.name', 'people.contact', 'people.alt_contact', 'people.del_address', 'people.del_postcode', 'people.bill_postcode', 'people.active', 'people.payterm', 'people.del_lat', 'people.del_lng', 'people.remark',
            DB::raw('DATE(people.created_at) AS created_at'), 'people.updated_at',
            'custcategories.name as custcategory_name', 'custcategories.map_icon_file', 'custcategory_groups.name AS custcategory_group_name',
            'profiles.id AS profile_id', 'profiles.name AS profile_name',
            'account_managers.name AS account_manager_name',
            'zones.name AS zone_name',
            'updater.name AS updated_by'
        );

        // reading whether search input is filled
        $people = $this->searchPeopleDBFilter($people, $request);

        if ($request->sortName) {
            $people = $people->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        // add user profile filters
        $people = $this->filterUserDbProfile($people);
        $people = $this->filterUserDbCustcategory($people);

        // condition (exclude all H code)
        $people = $people->where('people.cust_id', 'NOT LIKE', 'H%');

        if ($pageNum == 'All') {
            $people = $people->orderBy('people.created_at', 'desc')->get();
        } else {
            $people = $people->orderBy('people.created_at', 'desc')->paginate($pageNum);
        }

        // dd($people->toArray());
        $data = [
            'people' => $people,
        ];

        return $data;
    }

    // return creation api
    public function getCreationApi(Request $request)
    {
        $model = Person::with(['accountManager', 'custcategory', 'persontags', 'profile'])
                                ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'people.account_manager');

        $model = $this->searchPeopleFilter($model, $request);

        $model = $model->select(
            'account_manager.id AS account_manager_id', 'account_manager.name AS account_manager_name',
            DB::raw('COUNT(people.id) AS created_count'),
            DB::raw('MONTH(people.created_at) AS month'),
            DB::raw('DATE_FORMAT(people.created_at, "%b") AS month_name'),
            DB::raw('YEAR(people.created_at) AS year')
        );

        $model = $model->groupBy('year')->groupBy('month')->groupBy('account_manager.id');

        if($sortName = request('sortName')){
            $model = $model->orderBy($sortName, request('sortBy') ? 'asc' : 'desc');
        }else {
            $model = $model->orderBy('year', 'desc')->orderBy('month', 'desc')->orderBy('account_manager.name', 'asc');
        }
        // $model = $model->orderBy(DB::raw('YEAR(people.created_at)', 'desc'))->orderBy(DB::raw('MONTH(people.created_at)', 'desc'));

        $entries = $model->get();

        $dataArr = [];
        // dd($entries->toArray());
        if(count($entries) > 0) {
            foreach($entries as $entry) {
                if($entry->year and $entry->month) {
                    array_push($dataArr, [
                        'year' => $entry->year,
                        'month' => $entry->month,
                        'month_name' => $entry->month_name,
                        'account_manager_name' => $entry->account_manager_name ? $entry->account_manager_name : 'Unassigned',
                        'created_count' => $entry->created_count
                    ]);
                }
            }
        }

        return $dataArr;
    }

    public function getPersonUserId($user_id)
    {
        $person = Person::where('user_id', $user_id)->first();
        return $person;
    }

    public function index()
    {
        $month_options = $this->getMonthOptions();
        return view('person.index', compact('month_options'));
    }

    public function create()
    {
        return view('person.create');
    }

    public function store(PersonRequest $request)
    {
        $input = $request->all();
        $person = Person::create($input);
        $person->is_vending = $request->has('is_vending') ? 1 : 0;
        $person->is_dvm = $request->has('is_dvm') ? 1 : 0;
        $person->is_subsidiary = $request->has('is_subsidiary') ? 1 : 0;
        // default setting is dvm based on custcategory
        if ($person->custcategory) {
            if ($person->custcategory->name == 'V-Dir') {
                $person->is_dvm = 1;
            }
        }

        // $person->is_profit_sharing_report = $request->has('is_profit_sharing_report')? 1 : 0;
        $person->save();

        // copying is gst inclusive to individual person
        $person->is_gst_inclusive = $person->profile->is_gst_inclusive;
        $person->gst_rate = $person->profile->gst_rate;
        $person->account_manager = auth()->user()->id;
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

    public function editApi($id)
    {
        $person = Person::findOrFail($id);
        return $person;
    }

    // return files api by given person id(int $person_id)
    public function getFilesApi($person_id)
    {
        $files = StoreFile::wherePersonId($person_id)->oldest()->get();

        return $files;
    }

    // batch update files name (int $person_id)
    public function updateFilesName($person_id)
    {
        if(request()->has('removeFile')) {
            $this->removeFile(request()->input('removeFile'));

            return redirect()->action('PersonController@edit', $person_id);
        }
        $filesname = request('file_name');

        foreach ($filesname as $index => $filename) {
            if ($filename) {
                $file = StoreFile::findOrFail($index);
                $file->name = $filename;
                $file->save();
            }
        }

        Flash::success('Entries updated');

        return redirect()->action('PersonController@edit', $person_id);
    }

    // remove file api()
    public function removeFileApi()
    {
        $file = StoreFile::findOrFail(request('file_id'));
        $file->person->update();

        $file->delete();
    }

    public function update(PersonRequest $request, $id)
    {

        if($request->is_gst_inclusive == 'true') {
            $request->merge(array('is_gst_inclusive' => 1 ));
        }else if($request->is_gst_inclusive == 'false'){
            $request->merge(array('is_gst_inclusive' => 0 ));
        }

        $person = Person::findOrFail($id);

        // detect if changing profile, will copy the original is gst inclusive
        if ($person->profile_id != $request->profile_id) {
            $newprofile = Profile::findOrFail($request->profile_id);
            $request->merge(array('is_gst_inclusive' => $newprofile->is_gst_inclusive == 1 ? 1 : 0));
            $request->merge(array('gst_rate' => $newprofile->gst_rate ? $newprofile->gst_rate : 0));
        }
        // dd($request->all(), $newprofile->toArray());


        switch ($request->active) {
            case 'Activate':
                $request->merge(array('active' => 'Yes'));
                break;
            case 'Deactivate':
                $request->merge(array('active' => 'No'));
                break;
            case 'Pending':
                $request->merge(array('active' => 'Pending'));
                break;
            case 'New':
                $request->merge(array('active' => 'New'));
                break;
        }
        // dd($request->all());

        if($type = $request->type) {
            $request->merge(['is_dvm' => 0]);
            $request->merge(['is_vending' => 0]);
            $request->merge(['is_combi' => 0]);
            $request->merge(['is_subsidiary' => 0]);
            $request->merge(['is_non_freezer_point' => 0]);

            // default setting is dvm based on custcategory
            if($person->custcategory) {
                if ($person->custcategory->name == 'V-Dir') {
                    $request->merge(['is_dvm' => 1]);
                    $request->merge(['is_vending' => 0]);
                }else {
                    $request->merge([$type => 1]);
                }
            }else {
                $request->merge([$type => 1]);
            }
        }
        $request->merge(['updated_by' => auth()->user()->id]);
        $input = $request->all();
        unset($input['type']);
        $person->update($input);

        // serial number validation for vending
        if ($person->serial_number) {
            $this->validate($request, [
                'serial_number' => 'unique:people,serial_number,' . $person->id
            ], [
                'serial_number.unique' => 'The Serial Number has been taken'
            ]);
        }


        // $person->is_profit_sharing_report = $request->has('is_profit_sharing_report') ? 1 : 0;
        $person->save();
        if (!$person->is_vending and !$person->is_dvm) {
            $person->vending_piece_price = 0.00;
            $person->vending_monthly_rental = 0.00;
            $person->vending_profit_sharing = 0.00;
            $person->vending_monthly_utilities = 0.00;
            $person->vending_clocker_adjustment = 0.00;
            // $person->is_profit_sharing_report = 0;
            $person->save();
        }

        // tagging feature sync
        $this->syncPersonTags($person, $request);


        return Redirect::action('PersonController@edit', $person->id);
    }

    public function destroy($id)
    {
        $person = Person::findOrFail($id);
        if($person->transactions) {
            Flash::error('Transaction(s) found under this customer profile');
            return Redirect::action('PersonController@edit', $id);
        }else {
            $person->delete();
            return redirect('person');
        }
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
        $name = (Carbon::now()->format('dmYHi')) . $file->getClientOriginalName();

        Storage::put('person_asset/file/'.$name, file_get_contents($file->getRealPath()), 'public');
        $url = (Storage::url('person_asset/file/'.$name));
        $person->files()->create(['path' => $url]);
        $person->save();

        // $file->move('person_asset/file', $name);
        // $person->files()->create(['path' => "/person_asset/file/{$name}"]);

    }

    public function removeFile($id)
    {
        $file = StoreFile::findOrFail($id);
        $filename = $file->path;
        $path = public_path();
        if (!File::delete($path . $filename)) {
            $file->delete();
            return Redirect::action('PersonController@edit', $file->person_id);
        } else {
            $file->delete();
            return Redirect::action('PersonController@edit', $file->person_id);
        }
    }

    public function showTransac(Request $request, $person_id)
    {
        // dd('dude');
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;
        $person = Person::findOrFail($person_id);

        $transactions = DB::table('deals')
            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
            ->rightJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
            ->leftJoin('deliveryorders', 'deliveryorders.transaction_id', '=', 'transactions.id')
            ->select(
                DB::raw('(CASE WHEN transactions.gst=1 THEN (CASE WHEN transactions.is_gst_inclusive=1 THEN (transactions.total) ELSE (transactions.total * ((100 + transactions.gst_rate)/100)) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee THEN transactions.delivery_fee ELSE 0 END) AS total'),
                DB::raw('ROUND(SUM(CASE WHEN deals.divisor>1 THEN (items.base_unit * deals.dividend/deals.divisor) ELSE (deals.qty * items.base_unit) END)) AS pieces'),
                'transactions.delivery_fee', 'transactions.id AS id', 'transactions.status AS status', 'transactions.delivery_date AS delivery_date', 'transactions.driver AS driver', 'transactions.total_qty AS total_qty', 'transactions.pay_status AS pay_status', 'transactions.updated_by AS updated_by', 'transactions.updated_at AS updated_at', 'transactions.created_at AS created_at', 'transactions.pay_method', DB::raw('DATE(transactions.delivery_date) AS del_date'), 'transactions.po_no', 'transactions.del_postcode', 'transactions.name', 'transactions.contact', 'transactions.is_discard',
                'people.cust_id', 'people.company', 'people.id as person_id',
                'profiles.name AS profile_name',
                'transactions.gst',
                'transactions.gst_rate',
                'people.is_vending',
                'people.is_dvm',
                DB::raw('DATE(deliveryorders.delivery_date1) AS delivery_date1'),
                'transactions.is_deliveryorder'
            )
            ->where('people.id', '=', $person_id);

        // }

        // reading whether search input is filled
        $transactions = $this->searchTransactionDBFilter($transactions, $request);

        if ($request->sortName) {
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }
        // $transactions = $this->filterDriverView($transactions);

        $transactions = $transactions->latest('transactions.created_at')->groupBy('transactions.id');

        $totals = $this->calTotals($transactions);

        if ($pageNum == 'All') {
            $transactions = $transactions->get();
        } else {
            $transactions = $transactions->paginate($pageNum);
        }

        $profileTransactionsId = [];
        foreach (Transaction::wherePersonId($person->id)->get() as $transac) {
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

    // retrieve person tags
    public function getPersonTags($person_id = null)
    {

        $persontagattaches = DB::table('persontagattaches')
                            ->rightJoin('persontags', 'persontags.id', '=', 'persontagattaches.persontag_id')
                            ->leftJoin('people', function($join) use ($person_id){
                                $join->on('people.id', '=', 'persontagattaches.person_id');
                                    $join->where('people.id', '=', $person_id);

                            })
                            ->select('persontags.id', 'persontags.name', 'people.id AS person_id');

        $persontagattaches = $persontagattaches->get();

        // $persontagattaches

        return $persontagattaches;
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

        if ($person->cust_id[0] === 'H') {
            $person_id = 1643;
        }

        $personprice = DB::raw(
            "(
                                SELECT prices.retail_price, prices.quote_price, prices.item_id, people.cost_rate, people.id AS person_id FROM prices
                                LEFT JOIN people ON people.id = prices.person_id
                                WHERE people.id = " . $person_id . "
                                ) personprice"
        );

        if (auth()->user()->hasRole('franchisee')) {
            $personprice = DB::raw(
                "(
                                    SELECT fprices.retail_price, fprices.quote_price, fprices.item_id, people.cost_rate FROM fprices
                                    LEFT JOIN people ON people.id = fprices.person_id
                                    WHERE people.id = " . $person_id . "
                                    ) personprice"
            );
        }

        $items = DB::table('items')
            ->leftJoin($personprice, 'personprice.item_id', '=', 'items.id')
            ->select(
                'items.id AS item_id',
                'items.product_id',
                'items.name',
                'items.remark',
                'personprice.retail_price',
                'personprice.quote_price',
                'personprice.cost_rate'
            );

        if(auth()->user()->hasRole('watcher') or auth()->user()->hasRole('subfranchisee') or auth()->user()->hasRole('hd_user')) {
            $items = $items->where('personprice.quote_price', '>', 0);
        }

        $items = $items->where('items.is_active', 1)
            ->orderBy('items.product_id', 'asc')
            ->get();

        return $items;
    }

    public function getPersonCostRate($person_id)
    {
        $person = Person::findOrFail($person_id);

        return $person->cost_rate;
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
        if ($admin) {
            // $members = Person::where('cust_id', 'LIKE', 'D%')->orderBy('cust_id', 'asc')->pluck('name')->all();
            $members = Person::where('cust_id', 'LIKE', 'D%')->orderBy('cust_id', 'asc')->get();
        } else if ($member and !$admin) {
            // $members = $member->descendantsAndSelf()->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_id', 'asc')->pluck('name')->all();
            $members = $member->descendantsAndSelf()->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_id', 'asc')->get();
        }
        // return [''] + $members;
        return $members;
    }

    // retrieve Lat and Lng by person (int person_id)
    public function getDeliveryLatLng($person_id)
    {
        $person = Person::findOrFail($person_id);
        $latlng = [
            'lat' => $person->del_lat,
            'lng' => $person->del_lng
        ];
        return $latlng;
    }

    // store delivery latlng whenever has chance(int person_id)
    public function storeDeliveryLatLng($person_id)
    {
        $person = Person::findOrFail($person_id);
        $person->del_lat = request('lat');
        $person->del_lng = request('lng');
        $person->save();

        return $person;
    }

    // replicate the person particulars(int $person_id)
    public function replicatePerson($person_id)
    {
        $person = Person::findOrFail($person_id);
        $rep_person = $person->replicate();
        $find_already_replicate = Person::where('cust_id', 'LIKE', $person->cust_id . '-replicate-%');
        $rep_person->cust_id = $find_already_replicate->first() ? substr($find_already_replicate->max('cust_id'), 0, -1) . (substr($find_already_replicate->max('cust_id'), -1) + 1) : $person->cust_id . '-replicate-1';
        $rep_person->del_lat = null;
        $rep_person->del_lng = null;
        $rep_person->bank_id = null;
        $rep_person->account_manager = auth()->user()->id;
        $rep_person->save();


        // replicate pricelist
        $prices = $person->prices;
        foreach ($prices as $price) {
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

    // return person maintenance index()
    public function getPersonmaintenanceIndex()
    {
        return view('personmaintenance.index');
    }

    // retrieve person maintenance api()
    public function getPersonmaintenancesApi()
    {
        $personmaintenances = Personmaintenance::with(['updater', 'creator', 'person', 'vending']);

        // reading whether search input is filled
        if (request('title')) {
            $title = request('title');
            $personmaintenances = $personmaintenances->where('title', 'LIKE', '%' . $title . '%');
        }

        if (request('person_id')) {
            $person_id = request('person_id');
            $personmaintenances = $personmaintenances->where('person_id', $person_id);
        }

        if (request('created_from')) {
            $created_from = request('created_from');
            $personmaintenances = $personmaintenances->whereDate('created_at', '>=', $created_from);
        }

        if (request('created_to')) {
            $created_to = request('created_to');
            $personmaintenances = $personmaintenances->whereDate('created_at', '<=', $created_to);
        }

        $personmaintenances = $personmaintenances->orWhere('is_verify', null);

        if (request('sortName')) {
            $personmaintenances = $personmaintenances->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $personmaintenances = $personmaintenances->latest();
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if ($pageNum == 'All') {
            $personmaintenances = $personmaintenances->get();
        } else {
            $personmaintenances = $personmaintenances->paginate($pageNum);
        }

        $data = [
            'personmaintenances' => $personmaintenances
        ];

        return $data;
    }

    // create person maintenance()
    public function createPersonmaintenanceApi()
    {
        $personmaintenance = Personmaintenance::create([
            'person_id' => request('person_id'),
            'vending_id' => request('vending_id'),
            'title' => request('title'),
            'remarks' => request('remarks'),
            'created_by' => auth()->user()->id,
            'created_at' => Carbon::parse(request('created_at')),
            'complete_date' => request('complete_date'),
            'is_refund' => (request('refund_name') or request('refund_bank')) ? 1 : 0,
            'refund_name' => request('refund_name'),
            'refund_bank' => request('refund_bank'),
            'refund_account' => request('refund_account'),
            'refund_contact' => request('refund_contact'),
            'error_code' => request('error_code'),
            'lane_number' => request('lane_number')
        ]);
    }

    // update person maintenance()
    public function updatePersonmaintenanceApi()
    {
        // dd(request()->all());
        $personmaintenance = Personmaintenance::findOrFail(request('id'));

        $personmaintenance->update([
            'person_id' => request('person_id'),
            'vending_id' => request('vending_id'),
            'title' => request('title'),
            'remarks' => request('remarks'),
            'complete_date' => request('complete_date'),
            'created_at' => request('created_at'),
            'is_refund' => (request('refund_name') or request('refund_bank')) ? 1 : 0,
            'refund_name' => request('refund_name'),
            'refund_bank' => request('refund_bank'),
            'refund_account' => request('refund_account'),
            'refund_contact' => request('refund_contact'),
            'updated_by' => auth()->user()->id,
            'error_code' => request('error_code'),
            'lane_number' => request('lane_number')
        ]);
    }

    // update verification of job()
    public function verifyPersonmaintenanceApi()
    {
        $personmaintenance = Personmaintenance::findOrFail(request('personmaintenance_id'));
        $personmaintenance->is_verify = request('is_verify');
        $personmaintenance->save();
    }

    // get all people api()
    public function getPeopleOptionsApi()
    {
        $people = Person::with('vending')->orderBy('cust_id')->get();
        return $people;
    }

    // remove single personmaintenance api(integer id)
    public function destroyPersonmaintenanceApi($id)
    {
        $personmaintenance = Personmaintenance::findOrFail($id);
        $personmaintenance->delete();
    }

    // get customer tags
    public function getCustTagsIndexApi()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $query = Persontag::with('persontagattaches.person');

        if(request('tag_name')) {
            $query = $query->where('name', 'LIKE', '%'.request('tag_name').'%');
        }

        if(request('cust_id')) {
            $cust_id = request('cust_id');
            $query = $query->whereHas('persontagattaches.person', function($query) use ($cust_id) {
                $query->where('cust_id', 'LIKE', $cust_id.'%');
            });
        }

        if(request('company')) {
            $company = request('company');
            $query = $query->whereHas('persontagattaches.person', function($query) use ($company) {
                $query->where('company', 'LIKE', '%'.$company.'%');
            });
        }

        if(request('sortName')){
            $query = $query->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $query = $query->latest('created_at')->get();
        }else{
            $query = $query->latest('created_at')->paginate($pageNum);
        }

        return [
            'custtags' => $query
        ];
    }

    // destroy cust tags
    public function deleteCustTagApi($id)
    {
        $persontag = Persontag::findOrFail($id);

        if($persontag->persontagattaches) {
            foreach($persontag->persontagattaches as $persontagattach) {
                $persontagattach->delete();
            }
        }
        $persontag->delete();
    }

    // unbind single cust tag attachment
    public function unbindCustTagAttachment($id)
    {
        $persontagattach = Persontagattach::findOrFail($id);
        $persontagattach->delete();
    }

    // add new persontag
    public function createPersontagApi(Request $request)
    {
        $persontag_name = $request->persontag_name;

        if($persontag_name) {
            Persontag::create([
                'name' => $persontag_name
            ]);
        }
    }

    // bind the personid and persontag
    public function bindPersontagAttachesApi(Request $request)
    {
        $persontag_id = $request->persontag_id;
        $person_id = $request->person_id;

        if($persontag_id and $person_id) {
            Persontagattach::create([
                'person_id' => $person_id,
                'persontag_id' => $persontag_id
            ]);
        }
    }

    // get outlet visits by person id
    public function getOutletVisitsApi($personId)
    {
        $person = Person::with(['outletVisits' => function($query) {
            $query->orderBy('date', 'DESC')->orderBy('created_at', 'DESC');
        }, 'outletVisits.creator'])->find($personId);

        return $person;
    }

    // get outlet visits outcome
    public function getOutletVisitOutcomesApi()
    {
        $outcomes = OutletVisit::OUTCOMES;

        return $outcomes;
    }

    // save outlet visit form by person id
    public function saveOutletVisitPersonApi($person_id)
    {
        $person = Person::findOrFail($person_id);
        request()->merge(array('created_by' => auth()->user()->id));
        $outletVisit = OutletVisit::create(request()->all());
        $person->outletVisits()->save($outletVisit);
    }

    // delete outlet visit form by outletvisit id
    public function destroyOutletVisitPersonApi($outletVisitId)
    {
        $outletVisit = OutletVisit::findOrFail($outletVisitId);
        $outletVisit->delete();
    }

    public function batchAssignPeople(Request $request)
    {
        $people = $request->people;
        $assignForm = $request->assignForm;

        $transactions = [];
        if($people) {
            foreach($people as $person) {
                if(isset($person['check'])) {
                    if($person['check']) {
                        $person = Person::findOrFail($person['id']);
                        $name = $assignForm['name'];
                        $value = isset($assignForm[$assignForm['name']]) ? $assignForm[$assignForm['name']] : null;
                        if($value == '-1') {
                            $value = null;
                        }
                        switch($name) {
                            case 'custcategory':
                                $person->custcategory_id = $value;
                                break;
                            case 'account_manager':
                                $person->account_manager = $value;
                                break;
                            case 'zone_id':
                                $person->zone_id = $value;
                                break;
                            case 'tag_id':
                                if(!$assignForm['detach']) {
                                    $this->attachTagPerson($person->id, $value);
                                }else {
                                    $this->detachTagPerson($person->id, $value);
                                }
                                break;
                            case 'transactions':
                                $data['delivery_date'] = $assignForm['delivery_date'];
                                $data['driver'] = $assignForm['driver'];
                                $data['transremark'] = $assignForm['transremark'];
                                $transaction = $this->generateSingleInvoiceByPersonId($person->id, $data);
                                array_push($transactions, $transaction->id);
                                break;
                            case 'remark':
                                $person->remark = $value;
                                break;
                        }
                        $person->save();
                    }
                }
            }
        }
        // dd($transactions);
        Flash::success(count($transactions).' Invoices successfully created :'.implode(", ", $transactions));

        return [
            'transactions' => $transactions
        ];
    }

    // fast generate single invoice by person id
    public function generateSingleInvoiceByPersonId($person_id, $data)
    {
        $date = $data['delivery_date'];
        $driver = $data['driver'];
        $transremark = $data['transremark'];
        $person = Person::findOrFail($person_id);

        $transaction = Transaction::create([
            'delivery_date' => $date,
            'person_id' => $person->id,
            'status' => 'Confirmed',
            'pay_status' => 'Owe',
            'updated_by' => auth()->user()->name,
            'created_by' => auth()->user()->id,
            'del_postcode' => $person->del_postcode,
            'del_address' => $person->del_address,
            'del_lat' => $person->del_lat,
            'del_lng' => $person->del_lng,
            'driver' => $driver ? $driver : null,
            'transremark' => $transremark
        ]);

        $prevOpsDate = Operationdate::where('person_id', $person->id)->whereDate('delivery_date', '=', $date)->first();

        if($prevOpsDate) {
            $prevOpsDate->color = 'Orange';
            $prevOpsDate->save();
        }else {
            $opsdate = new Operationdate;
            $opsdate->person_id = $person->id;
            $opsdate->delivery_date = $date;
            $opsdate->color = 'Orange';
            $opsdate->save();
        }

        return $transaction;
    }

    // conditional filter parser(Collection $query, Formrequest $request)
    private function searchPeopleFilter($people, $request)
    {
        $franchisee_id = $request->franchisee_id;

        if ($cust_id = $request->cust_id) {
            if($request->strictCustId) {
                $people = $people->where('cust_id', 'LIKE', $cust_id . '%');
            }else {
                $people = $people->where('cust_id', 'LIKE', '%'. $cust_id . '%');
            }
        }
        if ($custcategory = $request->custcategory) {
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            if($request->excludeCustCat) {
                $people = $people->whereHas('custcategory', function($query) use ($custcategory) {
                    $query->whereNotIn('id', $custcategory);
                });
            }else {
                $people = $people->whereHas('custcategory', function($query) use ($custcategory) {
                    $query->whereIn('id', $custcategory);
                });
            }
        }
        if ($custcategory = $request->custcategory_group) {
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            if($request->exclude_custcategory_group) {
                $people = $people->whereHas('custcategory', function($query) use ($custcategory) {
                    $query->whereHas('custcategoryGroup', function($query) use ($custcategory) {
                        $query->whereNotIn('id', $custcategory);
                    });
                });
            }else {
                $people = $people->whereHas('custcategory', function($query) use ($custcategory) {
                    $query->whereHas('custcategoryGroup', function($query) use ($custcategory) {
                        $query->whereIn('id', $custcategory);
                    });
                });
            }
        }
        if ($company = $request->company) {
            $people = $people->where('company', 'LIKE', $company . '%');
        }
        if ($contact = $request->contact) {
            $people = $people->where(function ($query) use ($contact) {
                $query->where('contact', 'LIKE', '%' . $contact . '%')->orWhere('alt_contact', 'LIKE', '%' . $contact . '%');
            });
        }

        if($active = $request->active) {
            $actives = $active;
            if (count($actives) == 1) {
                $actives = [$actives];
            }
            $people = $people->whereIn('active', $actives);
        }

        if($tags = $request->tags) {
            if (count($tags) == 1) {
                $tags = [$tags];
            }
            $people = $people->whereHas('persontags', function($query) use ($tags) {
                $query->whereIn('persontags.id', $tags);
            });
        }

        if ($profile_id = $request->profile_id) {
            $people = $people->whereHas('profile', function($query) use ($profile_id) {
                $query->where('id', $profile_id);
            });
        }

                // add in franchisee checker
        if (auth()->user()->hasRole('franchisee') or auth()->user()->hasRole('hd_user') or auth()->user()->hasRole('watcher')) {
            $people = $people->whereIn('franchisee_id', [auth()->user()->id]);
        } else if (auth()->user()->hasRole('subfranchisee')) {
            $people = $people->whereIn('franchisee_id', [auth()->user()->master_franchisee_id]);
        } else if ($franchisee_id != null) {
            if($franchisee_id != 0) {
                $people = $people->where('franchisee_id', $franchisee_id);
            }else {
                $people = $people->where('francisee_id', 0);
            }
        }

        if($accountManager = $request->account_manager) {
            $people = $people->where('account_manager', $accountManager);
        }
        if($zoneId = $request->zone_id) {
            $people = $people->whereHas('zone', function($query) use ($zoneId) {
                $query->where('id', $zoneId);
            });
        }
        if($freezers = $request->freezers) {
            if (count($freezers) == 1) {
                $freezers = [$freezers];
            }
            $people = $people->whereHas('freezers', function($query) use ($freezers) {
                $query->whereIn('freezers.id', $freezers);
            });
        }

        return $people;
    }

    public function potentialIndex()
    {
        $month_options = $this->getMonthOptions();
        return view('person.potential-index', compact('month_options'));
    }

    // conditional filter parser(Collection $query, Formrequest $request)
    private function searchPeopleDBFilter($people, $request)
    {
        $cust_id = $request->cust_id;
        $strictCustId = $request->strictCustId;
        $custcategory = $request->custcategory;
        $custcategoryGroup = $request->custcategory_group;
        $excludeCustcategoryGroup = $request->exclude_custcategory_group;
        $company = $request->company;
        $contact = $request->contact;
        $active = $request->active;
        $tags = $request->tags;
        $profile_id = $request->profile_id;
        $franchisee_id = $request->franchisee_id;
        $accountManager = $request->account_manager;
        $zoneId = $request->zone_id;
        $excludeCustCat = $request->excludeCustCat;
        $freezers = $request->freezers;
        $createdMonth = $request->created_month;
        $updatedAt = $request->updated_at;
        $updatedBy = $request->updated_by;

        if ($cust_id) {
            if($strictCustId) {
                $people = $people->where('people.cust_id', 'LIKE', $cust_id . '%');
            }else {
                $people = $people->where('people.cust_id', 'LIKE', '%'. $cust_id . '%');
            }
        }
        if ($custcategory) {
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            if($excludeCustCat) {
                $people = $people->whereNotIn('custcategories.id', $custcategory);
            }else {
                $people = $people->whereIn('custcategories.id', $custcategory);
            }
        }
        if($custcategoryGroup) {
            if (count($custcategoryGroup) == 1) {
                $custcategoryGroup = [$custcategoryGroup];
            }
            if($excludeCustcategoryGroup) {
                // dd('here');
                $people = $people->whereNotIn('custcategory_groups.id', $custcategoryGroup);
            }else {
                $people = $people->whereIn('custcategory_groups.id', $custcategoryGroup);
            }
        }
        if ($company) {
            $people = $people->where('people.company', 'LIKE', '%' . $company . '%');
        }
        if ($contact) {
            $people = $people->where(function ($query) use ($contact) {
                $query->where('people.contact', 'LIKE', '%' . $contact . '%')->orWhere('people.alt_contact', 'LIKE', '%' . $contact . '%');
            });
        }
/*         if ($active) {
            $people = $people->where('people.active', 'LIKE', '%' . $active . '%');
        } */

        if($active) {
            $actives = $active;
            if (count($actives) == 1) {
                $actives = [$actives];
            }
            $people = $people->whereIn('people.active', $actives);
        }

        if($tags) {
            if (count($tags) == 1) {
                $tags = [$tags];
            }
            $people = $people->whereHas('persontags', function($query) use ($tags) {
                $query->whereIn('persontags.id', $tags);
            });
        }

        if ($profile_id) {
            $people = $people->where('profiles.id', $profile_id);
        }
                // add in franchisee checker
        if (auth()->user()->hasRole('franchisee') or auth()->user()->hasRole('hd_user') or auth()->user()->hasRole('watcher')) {
            $people = $people->whereIn('people.franchisee_id', [auth()->user()->id]);
        } else if (auth()->user()->hasRole('subfranchisee')) {
            $people = $people->whereIn('people.franchisee_id', [auth()->user()->master_franchisee_id]);
        } else if ($franchisee_id != null) {
            if($franchisee_id != 0) {
                $people = $people->where('people.franchisee_id', $franchisee_id);
            }else {
                $people = $people->where('people.francisee_id', 0);
            }
        }

        if($accountManager) {
            $people = $people->where('people.account_manager', $accountManager);
        }
        if($zoneId) {
            $people = $people->where('people.zone_id', $zoneId);
        }
        if($freezers) {
            if (count($freezers) == 1) {
                $freezers = [$freezers];
            }
            // $people = $people->whereHas('freezers')
            $people = $people->whereExists(function ($query) use ($freezers) {
                $query->select(DB::raw(1))
                      ->from('addfreezers')
                      ->whereRaw('addfreezers.person_id = people.id')
                      ->whereIn('addfreezers.freezer_id', $freezers);
            }) ;
        }
        if($createdMonth) {
            if($createdMonth == '-1') {
                $searchTiming = Carbon::today()->subYears(3)->startOfMonth()->toDateString();
                $people = $people->whereDate('people.created_at', '<=', $searchTiming);
            }else {
                $searchTiming = Carbon::createFromFormat('d-m-Y', '01-'.$createdMonth);
                $dateFrom = $searchTiming->copy()->startOfMonth()->toDateString();
                $dateTo = $searchTiming->copy()->endOfMonth()->toDateString();
                $people = $people->whereDate('people.created_at', '>=', $dateFrom)->whereDate('people.created_at', '<=', $dateTo);
            }
        }

        if($updatedAt) {
            $people = $people->whereDate('people.updated_at', '=', $updatedAt);
        }

        if($updatedBy) {
            $people = $people->whereHas('updatedBy', function($query) use ($updatedBy){
                $query->where('id', $updatedBy);
            });
        }

        return $people;
    }

    // conditional filter parser for transactions(Collection $query, Formrequest $request)
    private function searchTransactionDBFilter($transactions, $request)
    {
        $id = $request->id;
        $status = $request->status;
        $statuses = $request->statuses;
        $pay_status = $request->pay_status;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;
        $driver = $request->driver;
        $po_no = $request->po_no;

        if($id) {
            $transactions = $transactions->where('transactions.id', 'LIKE', '%' . $id . '%');
        }
        if($status) {
            $transactions = $transactions->where('transactions.status', 'LIKE', '%' . $status . '%');
        }
        if($statuses) {
            if(in_array("Delivered", $statuses)) {
                array_push($statuses, 'Verified Owe', 'Verified Paid');
            }
            $transactions = $transactions->whereIn('transactions.status', $statuses);
        }
        if($pay_status) {
            $transactions = $transactions->where('transactions.pay_status', 'LIKE', '%' . $pay_status . '%');
        }
        if($delivery_from) {
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }
        if($delivery_to) {
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }
        if($driver) {
            $transactions = $transactions->where('transactions.driver', 'LIKE', '%' . $driver . '%');
        }
        if($po_no) {
            $transactions = $transactions->where('transactions.po_no', 'LIKE', '%' . $po_no . '%');
        }
        return $transactions;
    }

    // calculate transactions totals (Collection $transactiions)
    private function calTotals($query)
    {
        $calTotalsQuery = clone $query;
        $transactions = $calTotalsQuery->get();
        $transactionsIdArr = [];
        $total_amount = 0;
        $total_paid = 0;
        $total_owe = 0;

        foreach ($transactions as $transaction) {
            array_push($transactionsIdArr, $transaction->id);
        }

        $total_amount = DB::table('transactions')
            ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
            ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
            ->whereIn('transactions.id', $transactionsIdArr)
            ->sum(DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));

        $total_paid = DB::table('transactions')
            ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
            ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
            ->whereIn('transactions.id', $transactionsIdArr)
            ->where('transactions.pay_status', 'Paid')
            ->sum(DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));

        $total_owe = DB::table('transactions')
            ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
            ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
            ->whereIn('transactions.id', $transactionsIdArr)
            ->where('transactions.pay_status', 'Owe')
            ->sum(DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                                                CASE
                                                WHEN transactions.is_gst_inclusive=0
                                                THEN total*((100+transactions.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));


        $totals = [
            'total_amount' => $total_amount,
            'total_paid' => $total_paid,
            'total_owe' => $total_owe
        ];
        return $totals;
    }

    // sync person tags(Person $person, Formrequest $request)
    private function syncPersonTags($person, $request)
    {

        $tags = $request->tags;

        if($tags) {

            foreach($tags as $index => $tag) {
                if(substr($tag, 0, 4) == 'New:') {
                    $persontag = Persontag::create([
                        'name' => substr($tag, strpos($tag, ":") + 1)
                    ]);
                    $persontagattach = Persontagattach::create([
                        'person_id' => $person->id,
                        'persontag_id' => $persontag->id
                    ]);
                    $tags[$index] = strval($persontag->id);
                }else {
                    $persontagattach = Persontagattach::where('person_id', $person->id)->where('persontag_id', $tag)->first();
                    if(!$persontagattach) {
                        $persontagattach = Persontagattach::create([
                            'person_id' => $person->id,
                            'persontag_id' => $tag
                        ]);
                    }
                }
            }
            // dd($tags);

            Persontagattach::whereNotIn('persontag_id', $tags)->where('person_id', $person->id)->delete();
        }else {
            $tags = Persontagattach::where('person_id', $person->id)->get();

            if($tags) {
                foreach($tags as $tag){
                    $tag->delete();
                }
            }

        }
    }

    // attach tag to person
    private function attachTagPerson($personId, $tagId)
    {
        if($tagId and $personId) {
            $prevPersonTagAttach = Persontagattach::where('person_id', $personId)->where('persontag_id', $tagId)->first();

            if(!$prevPersonTagAttach) {
                Persontagattach::create([
                    'person_id' => $personId,
                    'persontag_id' => $tagId
                ]);
            }
        }
    }

    // detach tag from person
    private function detachTagPerson($personId, $tagId)
    {
        $persontagattach = Persontagattach::where('person_id', $personId)->where('persontag_id', $tagId)->first();

        if($persontagattach) {
            $persontagattach->delete();
        }
    }

    // logic applicable for driver on transactions view
    private function filterDriverView($query)
    {
        if (auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
            $query = $query->whereDate('transactions.delivery_date', '>=', Carbon::today()->toDateString());
        }

        return $query;
    }
}