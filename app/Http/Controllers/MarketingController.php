<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Requests\MemberRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Controllers\Controller;
use App\Person;
use Auth;
use DB;
use PDF;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\User;
use App\DtdPrice;
use App\DtdTransaction;
use App\DtdDeal;
use App\Item;
use App\Deal;
use App\Transaction;
use App\Role;

class MarketingController extends Controller
{

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // DTD Setup
    public function indexSetup()
    {
        return view('market.setup.index');
    }

    public function indexSetupPriceApi()
    {
        $prices = DtdPrice::all();

        // dd($prices->toArray());

        return $prices->toJson();
    }

    public function storeSetupPrice(Request $request)
    {
        $input = $request->all();

        $retails = $request->retail;

        $quotes = $request->quote;

        foreach($retails as $index => $retail){

            if(($retail != null and $retail != '' and is_numeric($retail)) or ($quotes[$index] != null and $quotes[$index] != '' and is_numeric($quotes[$index]))){

                $price = DtdPrice::where('item_id', $index)->first();

                if(!$price){

                    $price = new DtdPrice;

                    $price->item_id = $index;
                }

                $price->retail_price = $retail;

                $price->quote_price = $quotes[$index];

                $price->updated_by = Auth::user()->name;

                $price->save();

                if($price->retail_price == 0 and $price->quote_price == 0){

                    $price->delete();
                }

            }
        }

        return view('market.setup.index');
    }

    // DTD Member (D)
    public function indexMember()
    {
        $self = Person::where('user_id', Auth::user()->id)->first();

        if(! $self){

            $self = null;
        }

        return view('market.member.index', compact('self'));
    }

    public function indexMemberApi()
    {
        $member = Person::where('user_id', Auth::user()->id)->first();

        $admin = Auth::user()->hasRole('admin');

        $all_members = Person::where('cust_id', 'LIKE', 'D%')->orderBy('cust_type', 'desc')->get();

        if($member){

            return $member->descendants()->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_type', 'desc')->get();

        }else if($admin or ($member and $admin)){

            return $all_members;

        }else{

            return '';
        }
    }

    public function createMember($level)
    {
        return view('market.member.create', compact('level'));
    }

    public function storeMember(MemberRequest $request)
    {

        $user_id = $this->createUser($request);

        if(! $user_id){

            return Redirect::action('MarketingController@createMember', $request->level);

        }

        $people = Person::where('cust_id', 'LIKE', 'D%');

        $first_person = Person::where('cust_id', 'D100001')->first();

        if(count($people) > 0 and $first_person){

            $latest_cust = (int) substr($people->max('cust_id'), 1) + 1;

            $latest_cust = 'D'.$latest_cust;

        }else{

            $latest_cust = 'D100001';
        }

        $request->merge(array('cust_type' => strtoupper($request->level)));

        $request->merge(array('user_id' => $user_id));

        $request->merge(array('cust_id' => $latest_cust));

        $request->merge(array('profile_id' => 1));

        $input = $request->all();

        $person = Person::create($input);

        if($request->assign_parent){

            $assign_to = Person::findOrFail($request->assign_parent);

            $person->makeChildOf($assign_to);

            $person->parent_name = $assign_to->name;

            $person->save();

        }else{

            if($latest_cust == 'D100001'){

                $person->makeRoot();

            }else{

                $creator = Person::where('user_id', Auth::user()->id)->first();

                if($creator){

                    $person->makeChildOf($creator);

                    $person->parent_name = $creator->name;

                }else{

                    $person->makeRoot();

                }

                $person->save();

            }
        }

        if($person){

            $ancestors = $person->getAncestors();

            $today = Carbon::today()->toDateString();

            $mail_list = array();

            foreach($ancestors as $ancestor){

                if($ancestor->email){

                    array_push($mail_list, $ancestor->email);

                }
            }

            $mail_list = implode(",", $mail_list);

            if($mail_list){

                $email = $mail_list;

                $sender = 'system@happyice.com.sg';

                $data = [

                    'person' => $person,
                    'today' => $today,
                ];

                Mail::send('email.new_member', $data, function ($message) use ($email, $sender)
                {
                    $message->from($sender);
                    $message->subject('New Member Creation (Door To Door Project)');
                    $message->setTo($email);
                });
            }

            Flash::success('User Successfully Registered, Please Check the Registered Email for Login Password');

            return Redirect::action('MarketingController@indexMember');

        }else{

            Flash::error('Please Try Again');

            return Redirect::action('MarketingController@createMember', $request->level);
        }
    }

    public function updateSelf(Request $request, $self_id)
    {
        $person = Person::findOrFail($self_id);

        if($request->parent_id){

            $new_parent = Person::findOrFail($request->parent_id);

            $person->makeChildOf($newperson);

            $person->parent_name = $new_parent->name;

            $person->save();
        }

        $input = $request->except(['parent_id']);

        $user = User::findOrFail($person->user_id);

        if($request->password){

            if($request->password === $request->password_confirmation){

                $user->password = $request->password;

                Flash::success('The Password has Changed');

            }else{

                Flash::error('The Password Confirmation is Invalid');

                return Redirect::action('MarketingController@indexMember');
            }
        }
        $person->update($input);

        $user->email = $request->email;

        $user->contact = $request->contact;

        $user->save();

        return Redirect::action('MarketingController@indexMember');

    }

    public function editMember($id)
    {
        $person = Person::findOrFail($id);

        return view('market.member.edit', compact('person'));
    }

    public function updateMember(Request $request, $id)
    {
        $input = $request->all();

        $person = Person::findOrFail($id);

        $user = User::findOrFail($person->user_id);

        if($request->parent_id){

            $newperson = Person::findOrFail($request->parent_id);

            if($person->parent_id != $newperson->id){

                $person->makeChildOf($newperson);

                $person->parent_name = $newperson->name;

                $person->save();
            }
        }

        $person->update($input);

        if($request->input('active')){

            $person->active = 'Yes';

        }else if($request->input('deactive')){

            $person->active = 'No';
        }

        if($request->input('reset')){

            $reset_pass = str_random(6);

            $user->password = $reset_pass;

            if($this->sendEmailReset($request, $reset_pass)){

                Flash::success('The Password has been Reset');

            }else{

                return Redirect::action('MarketingController@editMember', $id);
            }
        }

        $user->username = $request->company;

        $user->contact = $request->contact;

        $user->email = $request->email;

        $user->save();

        $person->save();

        return Redirect::action('MarketingController@indexMember');
    }


    // DTD Customers (H)
    public function indexCustomer()
    {
        return view('market.customer.index');
    }

    public function indexCustomerApi()
    {
        $person = Person::where('user_id', Auth::user()->id)->first();

        if($person){

            return $person->descendants()->where('cust_id', 'LIKE', 'H%')->reOrderBy('cust_id')->get();

        }else{

            return '';
        }
    }

    public function createCustomer()
    {
        return view('market.customer.create');
    }

    public function storeCustomer(CustomerRequest $request)
    {
        $people = Person::where('cust_id', 'LIKE', 'H%');

        $first_person = Person::where('cust_id', 'H100001')->first();

        if(count($people) > 0 and $first_person){

            $latest_cust = (int) substr($people->max('cust_id'), 1) + 1;

            $latest_cust = 'H'.$latest_cust;

        }else{

            $latest_cust = 'H100001';
        }

        $request->merge(array('cust_id' => $latest_cust));

        $request->merge(array('profile_id' => 1));

        $input = $request->all();

        $person = Person::create($input);

        if($request->assign_parent){

            $assign_to = Person::findOrFail($request->assign_parent);

            $person->makeChildOf($assign_to);

            $person->parent_name = $assign_to->name;

            $person->save();

        }else{

            $creator = Person::where('user_id', Auth::user()->id)->first();

            if($creator){

                $person->makeChildOf($creator);

                $person->parent_name = $creator->name;

            }else{

                $person->makeRoot();

            }

            $person->save();

        }

        if($person){

            Flash::success('Customer Successfully Created');

            return view('market.customer.index');

        }else{

            Flash::error('Please Try Again');

            return view('market.customer.create');
        }
    }

    public function editCustomer($id)
    {
        $person = Person::findOrFail($id);

        return view('market.customer.edit', compact('person'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $input = $request->all();

        $person = Person::findOrFail($id);

        $person->update($input);

        if($request->input('active')){

            $person->active = 'Yes';

        }else if($request->input('deactive')){

            $person->active = 'No';
        }

        $person->save();

        return view('market.customer.index');
    }

    // DTD Open Invoice
    public function indexDeal()
    {
        return view('market.deal.index');
    }

    public function indexDealApi(Request $request)
    {
        $transaction_id = $request->transaction_id;

        $cust_id = $request->cust_id;

        $company = $request->company;

        $status = $request->status;

        $del_from = $request->del_from;

        $del_to = $request->del_to;

        $query = DB::table('dtdtransactions')
                        ->leftJoin('people', 'dtdtransactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id');

        if($transaction_id){

            $query = $query->where('id', 'LIKE', '%'.$transaction_id.'%');
        }

        if($cust_id){

            $query = $query->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }

        if($company){

            $query = $query->where('people.company', 'LIKE', '%'.$company.'%');
        }

        if($status){

            $query = $query->where('status', 'LIKE', '%'.$status.'%');
        }

        if($del_from and $del_to){

            $query = $query->where('delivery_date', '>=', $del_from)->where('delivery_date', '<=', $del_to);

        }else{

            $query = $query->where('delivery_date', '>=', Carbon::today()->startOfWeek()->toDateString())

                            ->where('delivery_date', '<=', Carbon::today()->endOfWeek()->toDateString());
        }

        // auth only admin can see all or else own creation
        if(! Auth::user()->hasRole('admin')){

            $personid_arr = array();

            $people = Person::where('user_id', Auth::user()->id)->first()->getDescendantsAndSelf();

            foreach($people as $person){

                array_push($personid_arr, $person->id);

            }

            $query = $query->whereIn('people.id', $personid_arr);
        }

        $query = $query->select('dtdtransactions.id', 'people.cust_id', 'people.company', 'people.del_postcode', 'people.id as person_id', 'dtdtransactions.status', 'dtdtransactions.delivery_date', 'dtdtransactions.driver', 'dtdtransactions.total', 'dtdtransactions.total_qty', 'dtdtransactions.pay_status', 'dtdtransactions.updated_by', 'dtdtransactions.updated_at', 'profiles.name', 'dtdtransactions.created_at', 'profiles.gst', 'dtdtransactions.pay_method', 'dtdtransactions.note', 'dtdtransactions.transaction_id')
                ->latest('dtdtransactions.id')->get();

        $caltotal = $this->calTransactionTotal($query);

        $data = [

            'transactions' => $query,

            'totalAmount' => $caltotal
        ];

        return $data;
    }

    public function createDeal()
    {
        $people = Person::where('user_id', Auth::user()->id)->first();

        $adminview = Person::where('cust_id', 'LIKE', 'D%');

        $people = $people ? $people->descendantsAndSelf() : $adminview;

        return view('market.deal.create', compact('people'));
    }

    public function showDtdTransaction($person_id)
    {
        return DtdTransaction::with('person')->wherePersonId($person_id)->latest()->take(5)->get();
    }

    public function storeDeal(Request $request)
    {

        $request->merge(array('updated_by' => Auth::user()->name));

        $request->merge(array('created_by' => Auth::user()->name));

        $request->merge(['delivery_date' => Carbon::today()->addDay()]);

        $request->merge(['order_date' => Carbon::today()]);

        $input = $request->all();

        $dtdtransaction = DtdTransaction::create($input);
/*
        if($person_id[0] == 'D'){

            $transaction = Transaction::create($input);

        }*/

        return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);
    }

    public function editDeal($id)
    {
        $transaction = DtdTransaction::find($id);

        if(! $transaction){

            $transaction = '';

            $person = '';

        }else{

            $person = Person::findOrFail($transaction->person_id);
        }

        // retrieve manually to order product id asc
        $prices = DB::table('dtdprices')
                    ->leftJoin('items', 'dtdprices.item_id', '=', 'items.id')
                    ->select('dtdprices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id')
                    ->orderBy('product_id')
                    ->get();

        return view('market.deal.edit', compact('transaction', 'person', 'prices'));
    }

    public function showDeal($id)
    {
        $transaction = DtdTransaction::findOrFail($id);

        return $transaction;
    }

    public function getDealData($transaction_id)
    {
        $deals = DtdDeal::with('item')->where('transaction_id', $transaction_id)->get();

        return $deals;
    }

    public function update(Request $request, $dtdtrans_id)
    {

        $dtdtransaction = DtdTransaction::findOrFail($dtdtrans_id);

        $assign_cust = Person::findOrFail($dtdtransaction->person_id)->cust_id;

        if($request->confirm){

            $request->merge(array('status' => 'Confirmed'));

        }else if($request->del_paid){

            if(! $request->paid_by){

                $request->merge(array('paid_by' => Auth::user()->name));
            }

            $request->merge(array('paid_at' => Carbon::now()));

            if(! $request->driver){

                $request->merge(array('driver'=>Auth::user()->name));
            }

        }else if($request->del_owe){

            $request->merge(array('status' => 'Delivered'));

            $request->merge(array('pay_status' => 'Owe'));

            if(! $request->driver){

                $request->merge(array('driver'=>Auth::user()->name));
            }

            $request->merge(array('paid_by'=>null));

        }else if($request->paid){

            $request->merge(array('pay_status' => 'Paid'));

            if(! $request->paid_by){

                $request->merge(array('paid_by' => Auth::user()->name));
            }

            $request->merge(array('paid_at' => Carbon::now()));

        }elseif($request->unpaid){

            $request->merge(array('pay_status' => 'Owe'));

            $request->merge(array('paid_by' => null));

            $request->merge(array('paid_at' => null));

        }elseif($request->update){

            if($dtdtransaction->status === 'Confirmed'){

                $request->merge(array('driver' => null));

                $request->merge(array('paid_by' => null));

            }else if(($dtdtransaction->status === 'Delivered' or $dtdtransaction->status === 'Verified Owe') and $dtdtransaction->pay_status === 'Owe'){

                $request->merge(array('paid_by' => null));

            }

        }elseif($request->save_draft){

            $request->merge(array('status' => 'Draft'));
        }

        $request->merge(array('person_id' => $request->input('person_copyid')));

        $request->merge(array('updated_by' => Auth::user()->name));

        $dtdtransaction->update($request->all());

        $this->syncDtdDeal($request, $dtdtrans_id);

        $dtddeals = DtdDeal::where('transaction_id', $dtdtransaction->id)->get();

        if($request->submit_deal){

            if(count($dtddeals) == 0){

                Flash::error('Please entry the list');

                return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);

            }

            if(Carbon::today() >= Carbon::parse($request->delivery_date)){

                Flash::error('Delivery Date must be at least Tommorrow\'s Date');

                return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);

            }

            $request->merge(array('status' => 'Confirmed'));

            if($assign_cust[0] == 'D'){

                $request->merge(array('updated_by' => Auth::user()->name));

                $dtdtransaction->update($request->all());

                $this->syncOrder($dtdtransaction->id);
            }
        }

        if($assign_cust[0] == 'D' and $dtdtransaction->status == 'Confirmed'){

            $this->syncOrder($dtdtransaction->id);

        }

        return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);
    }

    // generate pdf invoice for transaction
    public function generateInvoice($id)
    {

        $transaction = DtdTransaction::findOrFail($id);

        $person = Person::findOrFail($transaction->person_id);

        $deals = DtdDeal::whereTransactionId($transaction->id)->get();

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

        $name = 'Inv('.$transaction->id.')_'.$person->cust_id.'_'.$person->company.'.pdf';

        $pdf = PDF::loadView('transaction.invoice', $data);

        $pdf->setPaper('a4');

        return $pdf->download($name);
    }

    public function destroyAjax($id)
    {
        $dtddeal = DtdDeal::findOrFail($id);

        $dtddeal->delete();

        return $dtddeal->id . 'has been successfully deleted';
    }

    public function sendEmailInv($id)
    {

        $transaction = DtdTransaction::findOrFail($id);

        if($transaction->transaction_id){

            $transaction = Transaction::findOrFail($transaction->transaction_id);

            $deals = Deal::whereTransactionId($transaction->id)->get();

            $totalprice = DB::table('deals')->whereTransactionId($transaction->id)->sum('amount');

            $totalqty = DB::table('deals')->whereTransactionId($transaction->id)->sum('qty');

        }else{

            $transaction = DtdTransaction::findOrFail($id);

            $deals = DtdDeal::whereTransactionId($transaction->id)->get();

            $totalprice = DB::table('dtddeals')->whereTransactionId($transaction->id)->sum('amount');

            $totalqty = DB::table('dtddeals')->whereTransactionId($transaction->id)->sum('qty');

        }

        $person = Person::findOrFail($transaction->person_id);

        if(! $person->email){

            Flash::error('Please set the email before sending');

            return Redirect::action('MarketingController@editDeal', $id);
        }

        $email = $person->email;

        $now = Carbon::now()->format('dmyhis');

        // $profile = Profile::firstOrFail();

        $data = [
            'transaction'   =>  $transaction,
            'person'        =>  $person,
            'deals'         =>  $deals,
            'totalprice'    =>  $totalprice,
            'totalqty'      =>  $totalqty,
            // 'profile'       =>  $profile,
        ];

        $name = 'Inv('.$transaction->id.')_'.$person->cust_id.'_'.$person->company.'('.$now.').pdf';

        $pdf = PDF::loadView('transaction.invoice', $data);

        $pdf->setPaper('a4');

        $sent = $pdf->save(storage_path('/invoice/'.$name));

        $store_path = storage_path('/invoice/'.$name);

        $sender = 'system@happyice.com.sg';

        $datamail = [

            'person' => $person,
            'url' => 'http://www.happyice.com.sg',

        ];

        Mail::send('email.send_invoice', $datamail, function ($message) use ($email, $sender, $store_path)
        {
            $message->from($sender);
            $message->subject('[Invoice] Happy Ice - Thanks for Your Support');
            $message->setTo($email);
            $message->attach($store_path);
        });

        if($sent){

            Flash::success('Successfully Sent');

        }else{

            Flash::error('Please Try Again');
        }

        return Redirect::action('MarketingController@editDeal', $id);

    }

    private function createUser($request)
    {
        $request->merge(array('username' => $request->company));

        // random password generator
        $ran_password = str_random(6);

        $request->merge(array('password' => $ran_password));

        if($request->email){

            $this->sendEmailUponRegistration($request, $ran_password);

            $user = User::create($request->all());

            $role = Role::where('name', 'marketer')->first();

            $user->type = 'marketer';

            $user->save();
            // assign marketer role
            $user->roles()->attach($role->id);

            return $user->id;

        }else{

            Flash::error('Please fill up the email');

            return null;
        }
    }

    private function loadInvoice($id){


    }

    // user get the credentials via email
    private function sendEmailUponRegistration($request, $password)
    {

        $email = $request->email;

        $sender = 'system@happyice.com.sg';

        $data = [

            'username' => $request->company,
            'password' => $password,
            'url' => 'http://www.happyice.com.sg/admin',

        ];

        Mail::send('email.marketing_registration', $data, function ($message) use ($email, $sender)
        {
            $message->from($sender);
            $message->subject('Thanks for Your Registration (Door To Door Project)');
            $message->setTo($email);
        });
    }

    // user get the credentials via email
    private function sendEmailReset($request, $password)
    {

        $email = $request->email;

        if(! $email){

            Flash::error('Please fill up the email');

            return false;

        }else{

            // $sender = 'daniel.ma@happyice.com.sg';
            $sender = 'system@happyice.com.sg';

            $data = [

                'username' => $request->company,
                'password' => $password,
                'url' => 'http://www.happyice.com.sg/admin',

            ];

            Mail::send('email.marketing_registration', $data, function ($message) use ($email, $sender)
            {
                $message->from($sender);
                $message->subject('Email Reset (Door To Door Project)');
                $message->setTo($email);
            });

            return true;
        }
    }

    // calculating gst and non for delivered total
    private function calTransactionTotal($arr)
    {
        $total_amount = 0;

        foreach($arr as $transaction){

            $person_gst = Person::findOrFail($transaction->person_id)->profile->gst;

            $total_amount += $person_gst == '1' ? round(($transaction->total * 107/100), 2) : $transaction->total;
        }

        return $total_amount;
    }

    // calculating qty total
    private function calQtyTotal($arr)
    {
        $total_qty = 0;

        foreach($arr as $transaction){

            $total_qty += $transaction->total_qty;

        }

        return $total_qty;
    }

    private function syncDtdDeal($request, $dtdtrans_id)
    {
        $qtys = $request->qty;

        $quotes = $request->quote;

        $amounts = $request->amount;

        $dtdtransaction = DtdTransaction::findOrFail($dtdtrans_id);

        $init_d = $dtdtransaction->person->cust_id[0] == 'D' ? true : false;

        $errors = array();

        if($qtys){

            foreach($qtys as $index => $qty){

                if($qty != NULL or $qty != 0){

                    $item = Item::findOrFail($index);

                    // inventory email notification for stock running low
                    if($init_d and $item->email_limit){

                        if($this->calOrderEmailLimit($qty, $item)){

                            if(! $item->emailed){

                                $this->sendEmailAlert($item);

                                // restrict only send 1 mail if insufficient
                                $item->emailed = true;

                                $item->save();
                            }

                        }else{
                            // reactivate email alert
                            $item->emailed = false;

                            $item->save();
                        }
                    }


                    if($init_d){

                        if($this->calOrderLimit($qty, $item)){

                            array_push($errors, $item->product_id.' - '.$item->name);

                        }else{

                            $dtddeal = new DtdDeal();

                            $dtddeal->transaction_id = $dtdtrans_id;

                            $dtddeal->item_id = $index;

                            $dtddeal->qty = $qty;

                            $dtddeal->amount = $amounts[$index];

                            $dtddeal->unit_price = $quotes[$index];

                            $dtddeal->qty_status = 1;

                            $dtddeal->save();

                            $dtddeal->deal_id = 'D'.$dtddeal->id;

                            $dtddeal->save();
                        }

                    }else{

                        $dtddeal = new DtdDeal();

                        $dtddeal->transaction_id = $dtdtrans_id;

                        $dtddeal->item_id = $index;

                        $dtddeal->qty = $qty;

                        $dtddeal->amount = $amounts[$index];

                        $dtddeal->unit_price = $quotes[$index];

                        $dtddeal->qty_status = 1;

                        $dtddeal->save();

                        $dtddeal->deal_id = 'D'.$dtddeal->id;

                        $dtddeal->save();

                    }
                }
            }
        }

        $dtddeals = DtdDeal::whereTransactionId($dtdtrans_id)->get();

        $deal_total = $dtddeals->sum('amount');

        $deal_totalqty = $dtddeals->sum('qty');

        $dtdtransaction->total = $deal_total;

        $dtdtransaction->total_qty = $deal_totalqty;

        $dtdtransaction->save();

        if(isset($errors)){

            if(count($errors) > 0){

                $errors_str = '';

                $errors_str = implode(" <br>", $errors);

                Flash::error('Stock Insufficient 缺货 (Please contact company 请联络公司): <br> '.$errors_str)->important();

            }

        }else{

            Flash::success('Successfully Added');
        }
    }

    private function syncOrder($dtdtransaction_id)
    {
        $dtdtransaction = DtdTransaction::findOrFail($dtdtransaction_id);

        if($dtdtransaction->transaction_id == null || $dtdtransaction->transaction_id == ''){

            $transaction = new Transaction();

        }else{

            $transaction = Transaction::findOrFail($dtdtransaction->transaction_id);
        }

        $transaction->total = $dtdtransaction->total;

        $transaction->delivery_date = $dtdtransaction->delivery_date;

        $transaction->status = 'Confirmed';

        $transaction->transremark = $dtdtransaction->transremark;

        $transaction->updated_by = $dtdtransaction->updated_by;

        $transaction->pay_status = 'Owe';

        $transaction->person_code = $dtdtransaction->person_code;

        $transaction->person_id = $dtdtransaction->person_id;

        $transaction->order_date = $dtdtransaction->order_date;

        $transaction->del_address = $dtdtransaction->del_address;

        $transaction->name = $dtdtransaction->name;

        $transaction->po_no = $dtdtransaction->po_no;

        $transaction->total_qty = $dtdtransaction->total_qty;

        $transaction->save();

        $dtdtransaction->transaction_id = $transaction->id;

        $dtdtransaction->save();

        // find and sync deals
        $deals = Deal::where('transaction_id', $transaction->id)->get();

        $dtddeals = DtdDeal::where('transaction_id', $dtdtransaction->id)->get();

        if(count($deals) != count($dtddeals)){

            $deal_arr = array();

            $dtddeal_arr = array();

            foreach($deals as $deal){

                array_push($deal_arr, $deal->id);
            }

            $dtdresults = DtdDeal::where('transaction_id', $dtdtransaction->id)->whereNotIn('deal_id', $deal_arr)->get();

            foreach($dtdresults as $dtddeal){

                $deal = new Deal();

                $deal->item_id = $dtddeal->item_id;

                $deal->transaction_id = $transaction->id;

                $deal->qty = $dtddeal->qty;

                $deal->amount = $dtddeal->amount;

                $deal->unit_price = $dtddeal->unit_price;

                $deal->qty_status = 1;

                $deal->save();

                $dtddeal->deal_id = $deal->id;

                $dtddeal->save();

                $this->dealSyncOrder($deal->item_id);
            }

            $dtddeals = DtdDeal::where('transaction_id', $dtdtransaction->id)->get();

            foreach($dtddeals as $dtddeal){

                array_push($dtddeal_arr, $dtddeal->deal_id);
            }

            $dealresults = Deal::where('transaction_id', $dtdtransaction->transaction_id)->whereNotIn('id', $dtddeal_arr)->get();

            foreach($dealresults as $dealresult){

                $dealresult->delete();
            }
        }
    }

    private function calOrderEmailLimit($qty, $item)
    {
        if($item->qty_now - $item->qty_order - $qty < $item->email_limit){

            return true;

        }else{

            return false;
        }
    }


    private function calOrderLimit($qty, $item)
    {
        if($item->qty_now - $item->qty_order - $qty < $item->lowest_limit ? $item->lowest_limit : 0){

            return true;

        }else{

            return false;
        }
    }


    // email alert for stock insufficient
    private function sendEmailAlert($item)
    {

        $today = Carbon::now()->format('d-m-Y H:i');

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

    private function dealSyncOrder($item_id)
    {
        $deals = Deal::where('qty_status', '1')->where('item_id', $item_id);

        $item = Item::findOrFail($item_id);

        $item->qty_order = $deals->sum('qty');

        $item->save();

    }
}
