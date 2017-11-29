<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Ftransaction;
use App\Person;
use Carbon\Carbon;
use DB;

// traits
use App\HasMonthOptions;
use App\HasProfileAccess;
use App\GetIncrement;

class FtransactionController extends Controller
{
	use HasMonthOptions, HasProfileAccess, GetIncrement;

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return index page()
	public function index()
	{
		return view('franchisee.index');
	}

	// return ftransaction index page api()
	public function indexApi()
	{
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;
        $ftransactions = DB::table('ftransactions')
                        ->leftJoin('people', 'ftransactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('users', 'users.id', '=', 'ftransactions.franchisee_id')
                        ->leftJoin('transactions', 'transactions.id', '=', 'ftransactions.transaction_id')
                        ->select(
                                    'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id', 'ftransactions.del_postcode',
                                    'ftransactions.status', 'ftransactions.delivery_date', 'ftransactions.driver',
                                    'ftransactions.total_qty', 'ftransactions.pay_status',
                                    'ftransactions.updated_by', 'ftransactions.updated_at', 'ftransactions.delivery_fee', 'ftransactions.ftransaction_id AS ftransaction_id', 'users.name',
                                    DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                CASE
                                                WHEN people.is_gst_inclusive=0
                                                THEN ftransactions.total*((100+profiles.gst_rate)/100)
                                                ELSE ftransactions.total
                                                END) ELSE ftransactions.total END) + (CASE WHEN ftransactions.delivery_fee>0 THEN ftransactions.delivery_fee ELSE 0 END), 2) AS total'),
                                    'profiles.id as profile_id', 'profiles.gst', 'people.is_gst_inclusive', 'profiles.gst_rate',
                                    'transactions.id AS transaction_id',
                                    DB::raw('(CASE WHEN transactions.id <> null THEN transactions.id ELSE ftransactions.ftransaction_id END) AS trans_id')
                                );

        // reading whether search input is filled
		if(request('id') or request('cust_id') or request('company') or request('status') or request('pay_status') or request('updated_by') or request('updated_at') or request('delivery_from') or request('delivery_to') or request('driver') or request('profile_id')){
            $ftransactions = $this->searchDBFilter($ftransactions);
        }

        // add user profile filters
        $ftransactions = $this->filterUserDbProfile($ftransactions);

        $total_amount = $this->calDBTransactionTotal($ftransactions);
        $delivery_total = $this->calDBDeliveryTotal($ftransactions);

        if(request('sortName')){
            $ftransactions = $ftransactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $ftransactions = $ftransactions->latest('ftransactions.created_at')->get();
        }else{
            $ftransactions = $ftransactions->latest('ftransactions.created_at')->paginate($pageNum);
        }

        $data = [
            'total_amount' => $total_amount + $delivery_total,
            'ftransactions' => $ftransactions,
        ];
        return $data;
	}

    // return ftransaction create page()
    public function create()
    {
        return view('franchisee.create');
    }

    // store ftarnsactions ()
    public function store()
    {
        $this->validate(request(), [
            'person_id' => 'required',
        ],[
            'person_id.required' => 'Please choose an option',
        ]);

        request()->merge(array('updated_by' => auth()->user()->name));
        request()->merge(array('delivery_date' => Carbon::today()));
        request()->merge(array('order_date' => Carbon::today()));
        request()->merge(array('franchisee_id' => auth()->user()->id));
        request()->merge(array('ftransaction_id' => $this->getFtransactionIncrement(request('franchisee_id'))));
        $input = request()->all();
        $ftransaction = Ftransaction::create($input);

        return redirect()->action('FtransactionController@edit', $ftransaction->id);
    }

    // show latest 5 ftransaction when person was selected(int person_id)
    public function showPersonTransac($person_id)
    {
        $ftransactions = DB::table('ftransactions')
                ->leftJoin('people', 'ftransactions.person_id', '=', 'people.id')
                ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                ->select(
                            'people.cust_id', 'people.company',
                            'people.name', 'people.id as person_id', 'ftransactions.del_postcode',
                            'ftransactions.status', 'ftransactions.delivery_date', 'ftransactions.driver',
                            'ftransactions.total_qty', 'ftransactions.pay_status',
                            'ftransactions.updated_by', 'ftransactions.updated_at', 'ftransactions.delivery_fee', 'ftransactions.id',
                            DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                CASE
                                                WHEN people.is_gst_inclusive=0
                                                THEN total*((100+people.gst_rate)/100)
                                                ELSE ftransactions.total
                                                END)
                                            ELSE ftransactions.total END) + (CASE WHEN ftransactions.delivery_fee>0 THEN ftransactions.delivery_fee ELSE 0 END), 2) AS total'),
                            'profiles.id as profile_id', 'profiles.gst', 'people.is_gst_inclusive', 'people.gst_rate'
                        )
                ->where('people.id', $person_id)
                ->orderBy('ftransactions.created_at', 'desc')
                ->take(5)
                ->get();
        return $ftransactions;
    }

    // return the edit page for the ftransaction(int ftransaction_id)
    public function edit($ftransaction_id)
    {
        $ftransaction = Ftransaction::findOrFail($ftransaction_id);
        $person = Person::findOrFail($ftransaction->person_id);

        $prices = DB::table('prices')
                    ->leftJoin('items', 'prices.item_id', '=', 'items.id')
                    ->select('prices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id')
                    ->where('prices.person_id', '=', $ftransaction->person_id)
                    ->where('items.is_active', 1)
                    ->orderBy('product_id')
                    ->get();

        return view('franchisee.edit', compact('ftransaction', 'person', 'prices'));
    }


    // pass value into filter search for DB (collection) [query]
    private function searchDBFilter($ftransactions)
    {
        if(request('id')){
            $ftransactions = $ftransactions->where('ftransactions.id', 'LIKE', '%'.request('id').'%');
        }
        if(request('cust_id')){
            $ftransactions = $ftransactions->where('people.cust_id', 'LIKE', '%'.request('cust_id').'%');
        }
        if(request('company')){
            $com = request('company');
            $ftransactions = $ftransactions->where(function($query) use ($com){
                $query->where('people.company', 'LIKE', '%'.$com.'%')
                        ->orWhere(function ($query) use ($com){
                            $query->where('people.cust_id', 'LIKE', 'D%')
                                    ->where('people.name', 'LIKE', '%'.$com.'%');
                        });
                });
        }
        if(request('status')){
            $ftransactions = $ftransactions->where('ftransactions.status', 'LIKE', '%'.request('status').'%');
        }
        if(request('pay_status')){
            $ftransactions = $ftransactions->where('ftransactions.pay_status', 'LIKE', '%'.request('pay_status').'%');
        }
        if(request('updated_by')){
            $ftransactions = $ftransactions->where('ftransactions.updated_by', 'LIKE', '%'.request('updated_by').'%');
        }
        if(request('updated_at')){
            $ftransactions = $ftransactions->where('ftransactions.updated_at', 'LIKE', '%'.request('updated_at').'%');
        }
        if(request('delivery_from') === request('delivery_to')){
            if(request('delivery_from') != '' and request('delivery_to') != ''){
                $ftransactions = $ftransactions->where('ftransactions.delivery_date', '=', request('delivery_from'));
            }
        }else{
            if(request('delivery_from')){
                $ftransactions = $ftransactions->where('ftransactions.delivery_date', '>=', request('delivery_from'));
            }
            if(request('delivery_to')){
                $ftransactions = $ftransactions->where('ftransactions.delivery_date', '<=', request('delivery_to'));
            }
        }
        if(request('driver')){
            $ftransactions = $ftransactions->where('ftransactions.driver', 'LIKE', '%'.request('driver').'%');
        }
        if(request('profile_id')){
            $ftransactions = $ftransactions->where('profiles.id', request('profile_id'));
        }
        if(request('sortName')){
            $ftransactions = $ftransactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $ftransactions;
    }

    // calculating gst and non for delivered total
    private function calDBTransactionTotal($query)
    {
        $total_amount = 0;
        $nonGst_amount = 0;
        $gst_exclusive = 0;
        $gst_inclusive = 0;
        $query1 = clone $query;
        $query2 = clone $query;
        $query3= clone $query;

        $nonGst_amount = $query1->where('profiles.gst', 0)->where('ftransactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND(ftransactions.total, 2)'));
        $gst_exclusive = $query2->where('profiles.gst', 1)->where('people.is_gst_inclusive', 0)->where('ftransactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND((ftransactions.total * (100 + profiles.gst_rate)/100), 2)'));
        $gst_inclusive = $query3->where('profiles.gst', 1)->where('people.is_gst_inclusive', 1)->where('ftransactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND(ftransactions.total, 2)'));

        $total_amount = $nonGst_amount + $gst_exclusive + $gst_inclusive;

        return $total_amount;
    }

    // calculate delivery fees total
    private function calDBDeliveryTotal($query)
    {
        $query3 = clone $query;
        $delivery_fee = $query3->where('ftransactions.status', '!=', 'Cancelled')->sum(DB::raw('ROUND(ftransactions.delivery_fee, 2)'));
        return $delivery_fee;
    }

    // create deals when there is input and update when deal exist(int ftransaction_id)
    private function syncFdeals($ftransaction_id)
    {

    }

}
