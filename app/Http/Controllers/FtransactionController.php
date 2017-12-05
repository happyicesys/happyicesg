<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Ftransaction;
use App\Fdeal;
use App\Person;
use App\Item;
use App\GeneralSetting;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use DB;
use PDF;

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
                        ->select(
                                    'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id',
                                    'ftransactions.del_postcode', 'ftransactions.id',
                                    'ftransactions.status', 'ftransactions.delivery_date', 'ftransactions.driver',
                                    'ftransactions.total_qty', 'ftransactions.pay_status',
                                    'ftransactions.updated_by', 'ftransactions.updated_at', 'ftransactions.delivery_fee', 'ftransactions.ftransaction_id', 'users.name', 'users.user_code',
                                    DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                CASE
                                                WHEN people.is_gst_inclusive=0
                                                THEN ftransactions.total*((100+profiles.gst_rate)/100)
                                                ELSE ftransactions.total
                                                END) ELSE ftransactions.total END) + (CASE WHEN ftransactions.delivery_fee>0 THEN ftransactions.delivery_fee ELSE 0 END), 2) AS total'),
                                    'profiles.id as profile_id', 'profiles.gst', 'people.is_gst_inclusive', 'profiles.gst_rate'
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
                ->leftJoin('users', 'people.franchisee_id', '=', 'users.id')
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
                            'profiles.id as profile_id', 'profiles.gst', 'people.is_gst_inclusive', 'people.gst_rate',
                            'users.user_code'
                        )
                ->where('people.id', $person_id)
                ->orderBy('ftransactions.created_at', 'desc')
                ->take(5)
                ->get();
        return $ftransactions;
    }

    // return the edit page for the ftransaction(int id)
    public function edit($id)
    {
        $ftransaction = Ftransaction::findOrFail($id);
        $person = Person::findOrFail($ftransaction->person_id);

        $fprices = DB::table('fprices')
                    ->leftJoin('items', 'fprices.item_id', '=', 'items.id')
                    ->select('fprices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id')
                    ->where('fprices.person_id', '=', $ftransaction->person_id)
                    ->where('items.is_active', 1)
                    ->orderBy('product_id')
                    ->get();

        return view('franchisee.edit', compact('ftransaction', 'person', 'fprices'));
    }


    // return ftransaction related components, fdeals (int id)
    public function editApi($id)
    {
        $total = 0;
        $subtotal = 0;
        $tax = 0;

        $ftransaction = Ftransaction::with('person')->findOrFail($id);

        $fdeals = DB::table('fdeals')
                    ->leftJoin('ftransactions', 'ftransactions.id', '=', 'fdeals.ftransaction_id')
                    ->leftJoin('people', 'people.id', '=', 'ftransactions.person_id')
                    ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                    ->leftJoin('items', 'items.id', '=', 'fdeals.item_id')
                    ->select(
                                'fdeals.ftransaction_id', 'fdeals.dividend', 'fdeals.divisor', 'fdeals.qty', 'fdeals.unit_price', 'fdeals.amount', 'fdeals.id AS deal_id',
                                'items.id AS item_id', 'items.product_id', 'items.name AS item_name', 'items.remark AS item_remark', 'items.is_inventory', 'items.unit',
                                'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                'ftransactions.del_postcode', 'ftransactions.status', 'ftransactions.delivery_date', 'ftransactions.driver',
                                DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                    CASE
                                                    WHEN people.is_gst_inclusive=0
                                                    THEN total*((100+people.gst_rate)/100)
                                                    ELSE ftransactions.total
                                                    END)
                                                ELSE ftransactions.total END) + (CASE WHEN ftransactions.delivery_fee>0 THEN ftransactions.delivery_fee ELSE 0 END), 2) AS total'),
                                'ftransactions.total_qty', 'ftransactions.pay_status','ftransactions.updated_by', 'ftransactions.updated_at', 'ftransactions.delivery_fee', 'ftransactions.id',
                                'profiles.id as profile_id', 'profiles.gst', 'people.is_gst_inclusive', 'people.gst_rate',
                                DB::raw('
                                    (CASE WHEN fdeals.divisor > 1
                                    THEN (items.base_unit * fdeals.dividend/fdeals.divisor)
                                    ELSE (items.base_unit * fdeals.qty)
                                    END) AS pieces
                                ')
                            )
                    ->where('fdeals.ftransaction_id', $ftransaction->id)
                    ->get();

        $subtotal = 0;
        $tax = 0;
        $total = number_format($ftransaction->total, 2);

        if($ftransaction->person->profile->gst) {
            if($ftransaction->person->is_gst_inclusive) {
                $total = number_format($ftransaction->total, 2);
                $tax = number_format($ftransaction->total - $ftransaction->total/((100 + $ftransaction->person->gst_rate)/ 100), 2);
                $subtotal = number_format($ftransaction->total - $tax, 2);
            }else {
                $subtotal = number_format($ftransaction->total, 2);
                $tax = number_format($ftransaction->total * ($ftransaction->person->gst_rate)/100, 2);
                $total = number_format(((float)$ftransaction->total + (float) $tax), 2);
            }
        }

        $delivery_fee = $ftransaction->delivery_fee;

        if($delivery_fee) {
            $total += number_format($delivery_fee, 2);
        }

        return $data = [
            'ftransaction' => $ftransaction,
            'fdeals' => $fdeals,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'delivery_fee' => $delivery_fee
        ];

    }

    // update franchisee edit page(int id)
    public function update($id)
    {
        // dynamic form arrays
        $quantities = request('qty');
        $amounts = request('amount');
        $quotes = request('quote');
        $ftransaction = Ftransaction::findOrFail($id);
        // find out deals created
        $fdeals = Fdeal::where('ftransaction_id', $ftransaction->id)->get();

        // retrieve different button press
        $button = request('submit_btn');
        switch($button) {
            case 'Save':
                request()->merge(array('status' => 'Pending'));
                break;
            case 'Delivered & Paid':
                request()->merge(array('status' => 'Delivered'));
                request()->merge(array('pay_status' => 'Paid'));
                request()->merge(array('paid_at' => Carbon::now()->format('Y-m-d h:i A')));
                if(!request('paid_by')){
                    request()->merge(array('paid_by' => auth()->user()->name));
                }
                if(! request('driver')){
                    request()->merge(array('driver'=> auth()->user()->name));
                }
                if(count($fdeals) == 0){
                    Flash::error('Please entry the list');
                    return redirect()->action('FtransactionController@edit', $ftransaction->id);
                }
                break;
            case 'Delivered & Owe':
                request()->merge(array('status' => 'Delivered'));
                request()->merge(array('pay_status' => 'Owe'));
                if(!request('paid_by')){
                    request()->merge(array('paid_by' => null));
                }
                if(! request('driver')){
                    request()->merge(array('driver'=> auth()->user()->name));
                }
                if(count($fdeals) == 0){
                    Flash::error('Please entry the list');
                    return redirect()->action('FtransactionController@edit', $ftransaction->id);
                }
                break;
            case 'Paid':
                request()->merge(array('pay_status' => 'Paid'));
                request()->merge(array('paid_at' => Carbon::now()->format('Y-m-d h:i A')));
                if(!request('paid_by')){
                    request()->merge(array('paid_by' => auth()->user()->name));
                }
                if(count($fdeals) == 0){
                    Flash::error('Please entry the list');
                    return redirect()->action('FtransactionController@edit', $ftransaction->id);
                }
                break;
            case 'Confirm':
                if(array_filter($quantities) != null and array_filter($amounts) != null) {
                    request()->merge(array('status' => 'Confirmed'));
                }else{
                    Flash::error('The list cannot be empty upon confirmation');
                    return redirect()->action('FtransactionController@edit', $ftransaction->id);
                }
                break;
            case 'Unpaid':
                request()->merge(array('pay_status' => 'Owe'));
                request()->merge(array('paid_at' => null));
                request()->merge(array('paid_by' => null));
                break;
            case 'Update':
                if($ftransaction->status === 'Confirmed'){
                    request()->merge(array('driver' => null));
                    request()->merge(array('paid_by' => null));
                    request()->merge(array('paid_at' => null));
                }else if(($ftransaction->status === 'Delivered' or $ftransaction->status === 'Verified Owe') and $ftransaction->pay_status === 'Owe'){
                    request()->merge(array('paid_by' => null));
                    request()->merge(array('paid_at' => null));
                }
                break;
        }

        request()->merge(array('person_id' => request()->input('person_copyid')));
        request()->merge(array('updated_by' => auth()->user()->name));
        $ftransaction->update(request()->all());

        $this->syncFdeal($ftransaction, $quantities, $amounts, $quotes);

        return redirect()->action('FtransactionController@edit', $ftransaction->id);
    }

    // delete ftransaction(int id)
    public function destroy($id)
    {
        $button = request('submit_btn');
        $ftransaction = Ftransaction::findOrFail($id);
        switch($button) {
            case 'Cancel Invoice':
                $ftransaction->cancel_trace = $ftransaction->status;
                $ftransaction->status = 'Cancelled';
                $ftransaction->updated_by = auth()->user()->name;
                $ftransaction->save();
                return redirect()->action('FtransactionController@edit', $ftransaction->id);
                break;
            case 'Delete Invoice':
                $transaction->delete();
                return redirect('franchisee');
        }
    }

    // undo cancelled ftransaction(int id)
    public function reverse($id)
    {
        $ftransaction = FTransaction::findOrFail($id);
        if($ftransaction->cancel_trace){
            $ftransaction->status = $ftransaction->cancel_trace;
            $ftransaction->cancel_trace = '';
            $ftransaction->updated_by = auth()->user()->name;
        }

        $ftransaction->save();
        return redirect()->action('FtransactionController@edit', $ftransaction->id);
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

    // delete single fdeal api (int fdeal_id)
    public function destroyFdealApi($fdeal_id)
    {

        $fdeal = FDeal::findOrFail($fdeal_id);
        $fdeal->delete();
        $ftransaction = Ftransaction::findOrFail($fdeal->ftransaction_id);
        $fdeals = Fdeal::where('ftransaction_id', $fdeal->ftransaction_id)->get();
        $deal_total = $fdeals->sum('amount');
        $deal_totalqty = $fdeals->sum('qty');
        $ftransaction->total = $deal_total;
        $ftransaction->total_qty = $deal_totalqty;
        $ftransaction->save();
        return $fdeal->id . 'has been successfully deleted';
    }

    // generate pdf invoice for ftransaction
    public function generateInvoice($id)
    {
        $ftransaction = Ftransaction::findOrFail($id);
        $person = Person::findOrFail($ftransaction->person_id);
        $fdeals = Fdeal::where('ftransaction_id', $ftransaction->id)->get();
        $totalprice = DB::table('fdeals')->where('ftransaction_id', $ftransaction->id)->sum('amount');
        $totalqty = DB::table('fdeals')->where('ftransaction_id', $ftransaction->id)->sum('qty');

        $data = [
            'inv_id' => $ftransaction->franchisee->user_code.$ftransaction->ftransaction_id,
            'transaction'   =>  $ftransaction,
            'person'        =>  $person,
            'deals'         =>  $fdeals,
            'totalprice'    =>  $totalprice,
            'totalqty'      =>  $totalqty,
        ];

        $name = $ftransaction->franchisee->user_code.'- Inv('.$ftransaction->ftransaction_id.')_'.$person->cust_id.'_'.$person->company.'.pdf';
        $pdf = PDF::loadView('transaction.invoice', $data);
        $pdf->setPaper('a4');
        $pdf->setOption('dpi', 85);
        return $pdf->download($name);
    }

    // send invoice email upon button clicked
    public function sendEmailInv($id)
    {
        $email_draft = GeneralSetting::firstOrFail()->DTDCUST_EMAIL_CONTENT;
        $ftransaction = Ftransaction::findOrFail($id);
        $self = auth()->user()->name;
        $fdeals = Fdeal::where('ftransaction_id', $ftransaction->id)->get();
        $totalprice = DB::table('fdeals')->where('ftransaction_id', $ftransaction->id)->sum('amount');
        $totalqty = DB::table('fdeals')->where('ftransaction_id', $ftransaction->id)->sum('qty');
        $person = Person::findOrFail($ftransaction->person_id);

        $email = $person->email;

        if(! $email){
            Flash::error('Please set the email before sending');
            return Redirect::action('FTransactionController@edit', $id);
        }else {
            if(strpos($email, ';') !== FALSE) {
                $email = explode(';', $email);
            }
        }

        $now = Carbon::now()->format('dmyhis');
        // $profile = Profile::firstOrFail();
        $data = [
            'inv_id' => $ftransaction->franchisee->user_code.'-'.$ftransaction->ftransaction_id,
            'transaction'   =>  $ftransaction,
            'person'        =>  $person,
            'deals'         =>  $fdeals,
            'totalprice'    =>  $totalprice,
            'totalqty'      =>  $totalqty,
        ];
        $name = $ftransaction->franchisee->user_code.'- Inv('.$ftransaction->ftransaction_id.')_'.$person->cust_id.'_'.$person->company.'('.$now.').pdf';
        $pdf = PDF::loadView('transaction.invoice', $data);
        $pdf->setPaper('a4');
        $sent = $pdf->save(storage_path('/invoice/'.$name));
        $store_path = storage_path('/invoice/'.$name);
        $sender = 'system@happyice.com.sg';
        $datamail = [
            'person' => $person,
            'transaction' => $ftransaction,
            'email_draft' => $email_draft,
            'self' => $self,
            'url' => 'http://www.happyice.com.sg',
        ];

        Mail::send('email.send_invoice', $datamail, function ($message) use ($email, $sender, $store_path, $ftransaction) {
            $message->from($sender);
            $message->subject('[Invoice ('.$ftransaction->franchisee->user_code.') '.$ftransaction->ftransaction_id.'] Happy Ice - Thanks for Your Support');
            $message->setTo($email);
            $message->attach($store_path);
        });

        if($sent){
            Flash::success('Successfully Sent');
        }else{
            Flash::error('Please Try Again');
        }

        return redirect()->action('FtransactionController@edit', $id);
    }

    // retrieve franchisee id if current user is franchisee or return null()
    public function getFranchiseeIdApi()
    {
        if(auth()->user()->hasRole('franchisee')) {
            $user = auth()->user()->id;
        }else {
            $user = null;
        }

        return $user;
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

    // create deals when there is input and update when deal exist(Collection ftransaction, Arr quantities, Arr amounts, Arr quotes)
    private function syncFdeal($ftransaction, $quantities, $amounts, $quotes)
    {
        if($quantities and $amounts){
            if(array_filter($quantities) != null and array_filter($amounts) != null){
                $errors = array();
                foreach($quantities as $index => $qty){

                    $dividend = 0;
                    $divisor = 1;

                    if(strpos($qty, '/') !== false) {
                        $dividend = explode('/', $qty)[0];
                        $divisor = explode('/', $qty)[1];
                        $qty = explode('/', $qty)[0]/ explode('/', $qty)[1];
                    }

                    if($qty != NULL or $qty != 0 ){
                        // inventory lookup before saving to deals
                        $item = Item::findOrFail($index);
                        $fdeal = new Fdeal();
                        $fdeal->ftransaction_id = $ftransaction->id;
                        $fdeal->item_id = $index;
                        $fdeal->dividend = $dividend ? $dividend : $qty;
                        $fdeal->divisor = $divisor;
                        $fdeal->amount = $amounts[$index];
                        $fdeal->unit_price = $quotes[$index];
                        if($item->is_inventory) {
                            $fdeal->qty = $qty;
                        }
                        $fdeal->save();
                    }
                }
            }
        }

        $fdeals = Fdeal::where('ftransaction_id', $ftransaction->id)->get();
        $deal_total = $fdeals->sum('amount');
        $deal_totalqty = $fdeals->sum('qty');
        $ftransaction->total = $deal_total;
        $ftransaction->total_qty = $deal_totalqty;
        $ftransaction->save();

        Flash::success('Successfully Added');
    }

}
