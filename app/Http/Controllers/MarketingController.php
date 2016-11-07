<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Requests\MemberRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\D2dOnlineSaleRequest;
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
use App\GeneralSetting;
use App\NotifyManager;
use App\EmailAlert;
use Maatwebsite\Excel\Facades\Excel;
use App\Postcode;
use App\D2dOnlineSale;

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
        $adminbool = false;
        $member_adminbool = false;
        $memberbool = false;
        $member = Person::where('user_id', Auth::user()->id)->first();
        $all_members = Person::where('cust_id', 'LIKE', 'D%')->with('manager')->orderBy('id', 'desc')->get();

        // find out is whether OM or normal d2d member
        if($member){
            if($member->cust_type === 'OM'){
                $member_adminbool = true;
            }else{
                $member_adminbool = false;
                $memberbool = true;
            }
        }

        if(Auth::user()->hasRole('admin')){
            $adminbool = true;
            $member_adminbool = false;
            $memberbool = false;
        }

        if($memberbool){
            return $member->descendants()->where('cust_id', 'LIKE', 'D%')->with('manager')->reOrderBy('id', 'desc')->get();
        }else if($adminbool or $member_adminbool){
            return $all_members;
        }else{
            return '';
        }
    }

    // return new member page
    public function createMember($level)
    {
        return view('market.member.create', compact('level'));
    }

    // create new members for dtd
    public function storeMember(MemberRequest $request)
    {
        $user_id = $this->createUser($request);
        if(! $user_id){
            return Redirect::action('MarketingController@createMember', $request->level);
        }

        $checkDupEmail = Person::where('cust_id', 'LIKE', 'D%')->where('email', $request->email)->first();
        if($checkDupEmail){
            Flash::error('The email has already been taken');
            return Redirect::action('MarketingController@createMember', $request->level);
        }

        $people = Person::withTrashed()->where('cust_id', 'LIKE', 'D%');
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
                    $person->save();
                }else{
                    $person->makeRoot();
                }
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

            // $mail_list = implode(",", $mail_list);
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

    // remove member permanantly
    public function destroyMember($person_id)
    {
        $person = Person::findOrFail($person_id);
        $person->delete();
        if($person->user_id){
            $user = User::findOrFail($person->user_id);
            $user->delete();
        }
        return Redirect::action('MarketingController@indexMember');
    }

    // self profile edit
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
        $input = $request->except('parent_id');
        $person = Person::findOrFail($id);
        $user = User::findOrFail($person->user_id);

        if($request->parent_id != null or $request->parent_id != ''){
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
        return Redirect::action('MarketingController@editMember', $id);
    }


    // DTD Customers (H)
    public function indexCustomer()
    {
        return view('market.customer.index');
    }

    public function indexCustomerApi()
    {
        $adminbool = false;
        $member_adminbool = false;
        $memberbool = false;
        $member = Person::where('user_id', Auth::user()->id)->first();
        $all_customers = Person::where('cust_id', 'LIKE', 'H%')->with('manager')->orderBy('id', 'desc')->get();

        // find out is whether OM or normal d2d member
        if($member){
            if($member->cust_type === 'OM'){
                $member_adminbool = true;
            }else{
                $member_adminbool = false;
                $memberbool = true;
            }
        }

        if(Auth::user()->hasRole('admin')){
            $adminbool = true;
            $member_adminbool = false;
            $memberbool = false;
        }

        // show results based on condition
        if($memberbool){
            return $member->descendants()->where('cust_id', 'LIKE', 'H%')->with('manager')->reOrderBy('id', 'desc')->get();
        }else if($adminbool or $member_adminbool){
            return $all_customers;
        }else{
            return '';
        }
    }

    public function createCustomer()
    {
        return view('market.customer.create');
    }

    public function createBatchCustomer()
    {
        return view('market.customer.batchcreate');
    }

    // transfer customers from one AB to another AB
    public function transferBatchCustomer()
    {
        return view('market.customer.batchtransfer');
    }

    // return single person descendant list for customers H ($id)
    public function getDescendantCustomer($id)
    {
        $person = Person::findOrFail($id);
        $customers = $person->descendants()->where('cust_id', 'LIKE', 'H%')->reOrderBy('id', 'desc')->get();
        return $customers;
    }

    // return descendant exclude the id parse in
    public function getDescMembersExcept(Request $request)
    {
        $manager_id = $request->manager_id;
        $desc_id = $request->desc_id;
        if($manager_id){
            $members = Person::findOrFail($manager_id)->descendants()->where('cust_id', 'LIKE', 'D%')->whereNotIn('id', [$desc_id])->reOrderBy('id', 'desc')->get();
        }else{
            $members = Person::where('cust_id', 'LIKE', 'D%')->whereNotIn('id', [$desc_id])->reOrderBy('id', 'desc')->get();
        }
        return $members;
    }

    // transfer customer post process(FormRequest)
    public function transferCustomer(Request $request)
    {
        $this->validate($request, [
            'trans_from' => 'required',
            'trans_to' => 'required',
        ],[
            'trans_from.required' => 'Please select a transfer from member',
            'trans_to.required' => 'Please select a  transfer to member'
        ]);
        $trans_from_collection = json_decode($request->trans_from);
        $trans_to_id = $request->trans_to;
        $from_member = Person::findOrFail($trans_from_collection->id);
        $to_member = Person::findOrFail($trans_to_id);

        $customers = $from_member->descendants()->where('cust_id', 'LIKE', 'H%')->reOrderBy('id', 'desc')->get();

        foreach($customers as $customer){
            $customer->makeChildOf($to_member);
            $customer->parent_name = $to_member->name;
            $customer->save();
        }
        Flash::success('Successfully Updated');
        return Redirect::action('MarketingController@indexCustomer');
    }

    public function storeCustomer(CustomerRequest $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'parent_id' => 'required'
        ],[
            'name.required' => 'Please fill in the name',
            'parent_id.required' => 'Please select the manager'
        ]);
        $people = Person::withTrashed()->where('cust_id', 'LIKE', 'H%');
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

        if($request->parent_id){
            $assign_to = Person::findOrFail($request->parent_id);
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

    public function storeBatchCustomer(Request $request)
    {
        $postals = $request->postalArr;
        $blocks = $request->blockArr;
        $floors = $request->floorArr;
        $units = $request->unitArr;
        $names = $request->nameArr;
        $contacts = $request->contactArr;
        $remarks = $request->remarkArr;

        foreach($names as $index => $name){
            if($name or $postals[$index] or $blocks[$index] or $floors[$index] or $units[$index] or $contacts[$index]){
                $people = Person::withTrashed()->where('cust_id', 'LIKE', 'H%');
                $first_person = Person::where('cust_id', 'H100001')->first();

                if(count($people) > 0 and $first_person){
                    $latest_cust = (int) substr($people->max('cust_id'), 1) + 1;
                    $latest_cust = 'H'.$latest_cust;
                }else{
                    $latest_cust = 'H100001';
                }
                $person = new Person();
                $person->cust_id = $latest_cust;
                $person->profile_id = 1;
                $person->payterm = 'C.O.D';
                $person->del_postcode = $postals[$index];
                $person->block = $blocks[$index];
                $person->floor = $floors[$index];
                $person->unit = $units[$index];
                $person->name = $names[$index];
                $person->contact = $contacts[$index];
                $person->remark = $remarks[$index];
                $person->save();
                $creator = Person::where('user_id', Auth::user()->id)->first();

                if($creator){
                    $person->makeChildOf($creator);
                    $person->parent_name = $creator->name;
                    $person->save();
                }else{
                    $person->makeRoot();
                }
            }
        }
        Flash::success('Customer Successfully Created');
        return view('market.customer.index');
    }

    public function editCustomer($id)
    {
        $person = Person::findOrFail($id);
        return view('market.customer.edit', compact('person'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        if($request->parent_id){
            if($request->parent_id != $person->parent_id){
                $newperson = Person::findOrFail($request->parent_id);
                $person->makeChildOf($newperson);
                $person->parent_name = $newperson->name;
                $person->save();
            }
        }

        $person->update($request->all());
        if($request->input('active')){
            $person->active = 'Yes';
        }else if($request->input('deactive')){
            $person->active = 'No';
        }
        $person->save();

        return Redirect::action('MarketingController@editCustomer', $id);
    }

    // remove member permanantly
    public function destroyCustomer($person_id)
    {
        $person = Person::findOrFail($person_id);

        $person->delete();

        return Redirect::action('MarketingController@indexCustomer');
    }

    // DTD Open Invoice
    public function indexDeal()
    {
        $commision_visible = true;
        $user_role = Person::whereUserId(Auth::user()->id)->first();
        if($user_role){
            if($user_role->cust_type === 'AB'){
                $commision_visible = false;
            }
        }
        return view('market.deal.index', compact('commision_visible'));
    }

    public function indexDealApi(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $adminaccess_bool = false;
        $transaction_id = $request->transaction_id;
        $cust_id = $request->cust_id;
        $company = $request->company;
        $status = $request->status;
        $del_from = $request->del_from;
        $del_to = $request->del_to;
        $parent_name = $request->parent_name;
        $type = $request->type;

        $query = DtdTransaction::with(['person', 'person.manager', 'person.profile', 'dtddeals.item']);

        if($transaction_id){
            $query = $query->where('transaction_id', 'LIKE', '%'.$transaction_id.'%');
        }

        if($cust_id){
            $query = $query->whereHas('person', function($query) use ($cust_id){
                $query->where('cust_id', 'LIKE', '%'.$cust_id.'%');
            });
        }

        if($company){
            $query = $query->whereHas('person', function($query) use ($company){
                $query->where('company', 'LIKE', '%'.$company.'%')->orWhere(function ($query) use ($company){
                        $query->where('cust_id', 'LIKE', 'D%')->where('name', 'LIKE', '%'.$company.'%');
                });
            });
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

        if($parent_name){
            $query = $query->whereHas('person.manager', function($query) use ($parent_name){
                $query->where('name', 'LIKE', '%'.$parent_name.'%');
            });
        }
        if($type){
            $query = $query->where('type', 'LIKE', '%'.$type.'%');
        }

        $self = Person::where('user_id', Auth::user()->id)->first();
        if($self){
            if($self->cust_type === 'OM'){
                $adminaccess_bool = true;
            }else{
                $adminaccess_bool = false;
            }
        }else{
            if(Auth::user()->hasRole('admin')){
                $adminaccess_bool = true;
            }else{
                $adminaccess_bool = false;
            }
        }
        // auth only admin can see all or else own creation
        if(! $adminaccess_bool){
            $personid_arr = array();
            $people = Person::where('user_id', Auth::user()->id)->first()->getDescendantsAndSelf();
            foreach($people as $person){
                array_push($personid_arr, $person->id);
            }
            $query = $query->whereHas('person', function($query) use ($personid_arr){
                $query->whereIn('id', $personid_arr);
            });
        }
        $caldeal = $this->calDealTotal($query);
        $calcomm = $this->calCommTotal($query);
        $query = $query->orderBy('id', 'desc')->get();
        $data = [
            'transactions' => $query,
            'totalDeal' => $caldeal,
            'totalComm' => $calcomm,
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

    public function createCommision()
    {
        $people = Person::where('user_id', Auth::user()->id)->first();
        $adminview = Person::where('cust_id', 'LIKE', 'D%');
        $people = $people ? $people->descendants() : $adminview;
        return view('market.deal.create_commision', compact('people'));
    }

    public function showDtdTransaction($person_id)
    {
        // return DtdTransaction::with('person')->wherePersonId($person_id)->latest()->take(5)->get();
        $dtdtransactions = DtdTransaction::with(['person', 'person.profile', 'dtddeals', 'dtddeals.item'])->wherePersonId($person_id)->wherehas('dtddeals', function($query){
            $query->wherehas('item', function($query){
                $query->whereNotIn('product_id', [301, 302]);
            });
        })->latest()->take(5)->get();
        return $dtdtransactions;
    }

    public function showDtdCommision($person_id)
    {
        // return DtdTransaction::with('person')->wherePersonId($person_id)->where('total', '>', '0')->latest()->take(5)->get();
        $dtdtransactions = DtdTransaction::with(['person', 'dtddeals', 'dtddeals.item'])->wherePersonId($person_id)->wherehas('dtddeals', function($query){
            $query->wherehas('item', function($query){
                $query->whereIn('product_id', [301, 302]);
            });
        })->latest()->take(5)->get();
        return $dtdtransactions;
    }

    public function storeDeal(Request $request)
    {
        $this->validate($request, [
            'person_id' => 'required',
        ],[
            'person_id.required' => 'Please choose an option',
        ]);

        $request->merge(array('updated_by' => Auth::user()->name));
        $request->merge(array('created_by' => Auth::user()->name));
        $request->merge(array('delivery_date' => Carbon::today()));
        $request->merge(array('order_date' => Carbon::today()));
        $request->merge(array('status' => 'Pending'));
        $request->merge(array('type' => 'Deal'));
        $input = $request->all();

        // find out customer or D code self chosen
        $person = Person::find($request->person_id);
        $dtdtransaction = DtdTransaction::create($input);
/*
        if($person->cust_id[0] === 'D'){
            $transaction = Transaction::create($input);
            $transaction->dtdtransaction_id = $dtdtransaction->id;
            $transaction->save();
            $dtdtransaction->transaction_id = $transaction->id;
            $dtdtransaction->save();
        }*/
        return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);
    }

    public function storeCommision(Request $request)
    {
        $this->validate($request, [
            'person_id' => 'required',
        ],[
            'person_id.required' => 'Please choose an option',
        ]);

        $request->merge(array('updated_by' => Auth::user()->name));
        $request->merge(array('created_by' => Auth::user()->name));
        $request->merge(array('delivery_date' => Carbon::today()));
        $request->merge(array('order_date' => Carbon::today()));
        $request->merge(array('status' => 'Pending'));
        $request->merge(array('type' => 'Commission'));
        $input = $request->all();


        // find out customer or D code self chosen
        $person = Person::find($request->person_id);
        $dtdtransaction = DtdTransaction::create($input);
/*
        if($person->cust_id[0] === 'D'){
            $transaction = Transaction::create($input);
            $transaction->dtdtransaction_id = $dtdtransaction->id;
            $transaction->save();
            $dtdtransaction->transaction_id = $transaction->id;
            $dtdtransaction->save();
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
/*        $prices = DB::table('dtdprices')
                    ->leftJoin('items', 'dtdprices.item_id', '=', 'items.id')
                    ->select('dtdprices.*', 'items.product_id', 'items.name', 'items.remark', 'items.id as item_id')
                    ->orderBy('product_id')
                    ->get();*/

        $prices = DtdPrice::whereHas('item', function($query) use ($transaction){
            if($transaction->type === 'Deal'){
                $query->whereNotIn('product_id', [301, 302]);
            }else if($transaction->type === 'Commission'){
                $query->whereIn('product_id', [301, 302]);
            }
            $query->orderBy('product_id');
        })->get();
        // edit form disable fields logic
        $noneditable = $this->checkFormEditable($transaction);

        return view('market.deal.edit', compact('transaction', 'person', 'prices', 'noneditable'));
    }

    // show independent deal
    public function showDeal($id)
    {
        $transaction = DtdTransaction::findOrFail($id);
        return $transaction;
    }

    // populate deals data in deals
    public function getDealData($transaction_id)
    {
        $deals = DtdDeal::with('item')->where('transaction_id', $transaction_id)->get();
        return $deals;
    }

    // update door to door transactions
    public function update(Request $request, $dtdtrans_id)
    {
        $dtdtransaction = DtdTransaction::findOrFail($dtdtrans_id);

        // date validation check
        $this->validate($request, [
            'order_date' => 'required|date|after:yesterday',
            'delivery_date' => 'required|date',
        ],[
            'order_date.required' => 'Please fill in the Order Date',
            'order_date.after' => 'Order Date must at least today',
            'delivery_date.required' => 'Please fill in the Delivery Date',
        ]);

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
        // dd($request->all());
        $request->merge(array('person_id' => $request->input('person_copyid')));
        $request->merge(array('updated_by' => Auth::user()->name));
        $dtdtransaction->update($request->all());
        $this->syncDtdDeal($request, $dtdtrans_id);
        $dtddeals = DtdDeal::where('transaction_id', $dtdtransaction->id)->get();
        if($request->submit_deal){
            if(count($dtddeals) == 0){
                dd('here');
                Flash::error('Please entry the list');
                return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);
            }
            $request->merge(array('status' => 'Confirmed'));
            if($dtdtransaction->person->cust_id[0] === 'D'){
                $request->merge(array('updated_by' => Auth::user()->name));
                $dtdtransaction->update($request->all());
                $this->syncTransaction($dtdtransaction->id, $request);
            }
        }

        if($dtdtransaction->person->cust_id[0] === 'D' and $dtdtransaction->status === 'Confirmed'){
            $this->syncTransaction($dtdtransaction->id, $request);
        }
        return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);
    }

    // generate pdf invoice for transaction
    public function generateInvoice($id)
    {
        $transaction = DtdTransaction::findOrFail($id);
        $person = Person::findOrFail($transaction->person_id);
        $deals = DtdDeal::whereTransactionId($transaction->id)->get();
        $totalprice = DB::table('dtddeals')->whereTransactionId($transaction->id)->sum('amount');
        $totalqty = DB::table('dtddeals')->whereTransactionId($transaction->id)->sum('qty');
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

    // delete D/H created deals
    public function destroyAjax($id)
    {
        $dtddeal = DtdDeal::findOrFail($id);
        $dtddeal->delete();
        return $dtddeal->id . 'has been successfully deleted';
    }

    // send invoice to D/ H email upon button clicked
    public function sendEmailInv($id)
    {
        $email_draft = GeneralSetting::firstOrFail()->DTDCUST_EMAIL_CONTENT;
        $transaction = DtdTransaction::findOrFail($id);
        $self = Auth::user()->name;

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
        $data = [
            'transaction'   =>  $transaction,
            'person'        =>  $person,
            'deals'         =>  $deals,
            'totalprice'    =>  $totalprice,
            'totalqty'      =>  $totalqty,
        ];
        $name = 'Inv('.$transaction->id.')_'.$person->cust_id.'_'.$person->company.'('.$now.').pdf';
        $pdf = PDF::loadView('transaction.invoice', $data);
        $pdf->setPaper('a4');
        $sent = $pdf->save(storage_path('/invoice/'.$name));
        $store_path = storage_path('/invoice/'.$name);
        $sender = 'system@happyice.com.sg';
        $datamail = [
            'person' => $person,
            'transaction' => $transaction,
            'email_draft' => $email_draft,
            'self' => $self,
            'url' => 'http://www.happyice.com.sg',
        ];

        Mail::send('email.send_invoice', $datamail, function ($message) use ($email, $sender, $store_path, $transaction)
        {
            $message->from($sender);
            $message->subject('[Invoice - '.$transaction->id.'] Happy Ice - Thanks for Your Support');
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

    // convert transaction to cancelled
    public function destroy($id, Request $request)
    {
        $dtdtransaction = DtdTransaction::findOrFail($id);
        if($request->input('form_delete')){
            if($dtdtransaction->status === 'Confirmed'){
                if($dtdtransaction->transaction_id) {
                    $transaction = Transaction::findOrFail($dtdtransaction->transaction_id);
                    $transaction->cancel_trace = $transaction->status;
                    $transaction->status = 'Cancelled';
                    $transaction->save();
                    $this->dealDeleteMultiple($transaction->id);
                }
            }
            $dtdtransaction->cancel_trace = $dtdtransaction->status;
            $dtdtransaction->status = 'Cancelled';
            $dtdtransaction->save();
            return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);
        }else if($request->input('form_wipe')){
            $dtdtransaction->delete();
            if($dtdtransaction->transaction_id){
                $transaction = Transaction::findOrFail($dtdtransaction->transaction_id);
                $transaction->delete();
            }
            return redirect('market/deal');
        }
    }

    public function reverse($id)
    {
        $dtdtransaction = DtdTransaction::findOrFail($id);
        if($dtdtransaction->transaction_id){
            $transaction = Transaction::findOrFail($dtdtransaction->transaction_id);
            $deals = Deal::where('transaction_id', $transaction->id)->where('qty_status', '3')->get();
            if($transaction->cancel_trace){
                $this->dealUndoDelete($transaction->id);
                $transaction->status = $transaction->cancel_trace;
                $transaction->cancel_trace = '';
                $transaction->updated_by = Auth::user()->name;
            }
            $transaction->save();
        }
        $dtdtransaction->status = $dtdtransaction->cancel_trace;
        $dtdtransaction->cancel_trace = '';
        $dtdtransaction->updated_by = Auth::user()->name;
        $dtdtransaction->save();
        return Redirect::action('MarketingController@editDeal', $dtdtransaction->id);
    }

    // direct customer email draft view
    public function emailDraft()
    {
        $email_draft = GeneralSetting::firstOrFail()->DTDCUST_EMAIL_CONTENT;
        return view('market.customer.emaildraft', compact('email_draft'));
    }

    // update email draft for customer
    public function updateEmailDraft(Request $request)
    {
        $email_draft = GeneralSetting::firstOrFail();
        $email_draft->DTDCUST_EMAIL_CONTENT = $request->content;
        $email_draft->save();
        return view('market.customer.index');
    }

    // populate manager notifications list
    public function notifyManagerIndex($person_id)
    {
        $notifications = NotifyManager::where('person_id', $person_id)->get();
        $person = Person::findOrFail($person_id);
        return view('market.customer.notify', compact('notifications', 'person'));
    }

    // store notify manager data
    public function storeNotification(Request $request, $person_id)
    {
        $person = Person::findOrFail($person_id);
        $manager = Person::where('id', $person->parent_id)->first();
        $person_send = Auth::user();

        if(! $manager->email){
            Flash::error('No email detected for this customer\'s manager');
            return Redirect::action('MarketingController@notifyManagerIndex', $person->id);
        }

        $request->merge(array('person_id', $request->person_id));
        $notification = NotifyManager::create($request->all());
        $email = $manager->email;
        $sender = 'system@happyice.com.sg';
        $data = [
            'notification' => $notification,
            'person' => $person,
        ];

        Mail::send('email.notify_manager', $data, function ($message) use ($email, $sender, $person_send, $person)
        {
            $message->from($sender);
            $message->subject('[HappyIce] - Notification from '.$person_send->name.' for Customer '.$person->cust_id.' - '.$person->name);
            $message->setTo($email);
        });

        Flash::success('Successfully Sent');
        return Redirect::action('MarketingController@notifyManagerIndex', $person->id);
    }

    // destroy notify manager data
    public function destroyNotification($id)
    {
        $notification = NotifyManager::findOrFail($id);
        $notification->delete();
        return Redirect::action('MarketingController@notifyManagerIndex', $notification->person_id);
    }

    // generate logs file for individual deals
    public function generateLogs($id)
    {
        $transaction = DtdTransaction::findOrFail($id);
        $transHistory = $transaction->revisionHistory;
        return view('market.deal.log', compact('transaction', 'transHistory'));
    }

    // return all postcodes list
    public function getPostcodes()
    {
        $dtdperson = Person::whereUserId(Auth::user()->id)->first();
        $dtdrole = '';
        if($dtdperson){
            $dtdrole = $dtdperson->cust_type;
        }
        if(Auth::user()->hasRole('admin') or $dtdrole === 'OM' or $dtdrole === 'OE'){
            $postcodes = Postcode::with('person')->get();
        }else{
            // setup array to fetch self and descendants id
            $descAndSelfID = [];
            $descAndSelf = $dtdperson->getDescendantsAndSelf();
            foreach($descAndSelf as $member){
                array_push($descAndSelfID, $member->id);
            }
            // filter through postcode that suppose to be shown
            $postcodes = Postcode::with('person')->whereIn('person_id', $descAndSelfID)->get();
        }
        return $postcodes;
    }

    // return d2d members list
    public function getAllMembers()
    {
        $dtdperson = Person::whereUserId(Auth::user()->id)->first();
        if($dtdperson){
            $dtdrole = $dtdperson->cust_type;
        }else{
            $dtdrole = '';
        }
        if(Auth::user()->hasRole('admin') or $dtdrole === 'OM' or $dtdrole === 'OE'){
            $people = Person::where('cust_id', 'LIKE', 'D%')->get();
        }else{
            $people = Person::whereUserId(Auth::user()->id)->first()->descendantsAndSelf()->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_id')->get();
        }
        return $people;
    }

    // store postcode excel file import
    public function storePostcode(Request $request)
    {
        $this->validate($request, [
            'postcode_excel' => 'required|max:500000'
        ], [
            'postcode_excel.required' => 'Please insert the postcode excel file',
            // 'postcode_excel.mimes' => 'Only excel file is accepted',
            'postcode_excel.max' => 'The excel file cannot exceed 5 mb',
        ]);
        $file = $request->file('postcode_excel');
        $excel = Excel::load($file, function($reader) {
            foreach($reader->all() as $row){
                if($row->postcode != '' and $row->postcode != null){
                    // find out the postcode of the row is exisiting or new
                    $postcode = Postcode::whereValue($row->postcode)->first();
                    if(!$postcode){
                        $postcode = Postcode::create(['value'=>$row->postcode]);
                    }
                    $postcode->block = $row->block;
                    $postcode->area_code = $row->area_code;
                    $postcode->area_name = $row->area_name;
                    $postcode->group = $row->AM;
                    $postcode->street = $row->street;

                    $assign_to = $row->assign_to;
                    if($assign_to){
                        $person = Person::where('cust_id', 'LIKE', 'D%')->where('name', 'LIKE', '%'.$assign_to.'%')->first();
                        if($person){
                            $postcode->person_id = $person->id;
                            $postcode->save();
                        }else{
                            $postcode->person_id = null;
                            $postcode->save();
                        }
                    }
                    $postcode->save();
                }
            }
        });
        if($excel){
            Flash::success('Successfully synced');
        }else{
            Flash::error('Please check the file again');
        }
        return Redirect::action('MarketingController@indexSetup');
    }

    // update postcode attach person id
    public function updatePostcodeForm(Request $request)
    {
        $checked = $request->checkbox;
        $manager = $request->manager;
        if($checked){
            foreach($checked as $index => $check){
                $postcode = Postcode::findOrFail($index);
                if($request->delete){
                    $postcode->delete();
                }else{
                    if($manager[$index]){
                        $postcode->person_id = $manager[$index];
                    }else{
                        $postcode->person_id = null;
                    }
                    $postcode->save();
                }
            }
            Flash::success('Successfully updated');
        }else{
            Flash::error('Please select the list to edit');
        }
        return Redirect::action('MarketingController@indexSetup');
    }

    // return create view of d2d online order sales item
    public function createDtdOnlineItems()
    {
        return view('market.d2ditem.create');
    }

    // store d2d online order sales item data
    public function storeDtdOnlineItem(D2dOnlineSaleRequest $request)
    {
        // preset sequence
        if(!$request->sequence){
            $max_sequence = (int) D2dOnlineSale::max('sequence') + 1;
        }else{
            $max_sequence = $this->createSequence($request->sequence);
        }
        $request->merge(array('sequence' => $max_sequence));

        // preset qty divisor
        if(!$request->qty_divisor){
            $reqeust->merge(array('qty_divisor' => 1));
        }

        $salesitem = D2dOnlineSale::create($request->all());
        if($salesitem){
            Flash::success('D2d online sales item was added successfully');
        }else{
            Flash::error('Please try again');
        }
        return Redirect::action('MarketingController@indexSetup');
    }

    // return d2d online sales item edit page(int $id)
    public function editDtdOnlineItem($id)
    {
        $salesitem = D2dOnlineSale::findOrFail($id);
        return view('market.d2ditem.edit', compact('salesitem'));
    }

    // update d2d online sales item from edit page(Formrequest $request, int $id)
    public function updateDtdOnlineItem(D2dOnlineSaleRequest $request, $id)
    {
        $salesitem = D2dOnlineSale::findOrFail($id);

        if($request->sequence != $salesitem->sequence){
            // if the request seq is bigger than original, move to right
            if($request->sequence > $salesitem->sequence){
                $right_moves = D2dOnlineSale::where('sequence', '>', $salesitem->sequence)->where('sequence', '<=', $request->sequence)->get();
                if($right_moves){
                    foreach($right_moves as $right_move){
                        $right_move->sequence -= 1;
                        $right_move->save();
                    }
                }
            }else{
                $left_moves = D2dOnlineSale::where('sequence', '>=', $request->sequence)->where('sequence', '<', $salesitem->sequence)->get();
                if($left_moves){
                    foreach($left_moves as $left_move){
                        $left_move->sequence += 1;
                        $left_move->save();
                    }
                }
            }
        }
        if(!$request->qty_divisor){
            $request->merge(array('qty_divisor' => 1));
        }
        $salesitem->update($request->all());
        return Redirect::action('MarketingController@indexSetup');
    }

    // delete salesitem by id (int $id)
    public function destroyDtdOnlineItem($id)
    {
        $salesitem = D2dOnlineSale::findOrFail($id);
        $salesitem->delete();
        $remains = D2dOnlineSale::where('sequence', '>', $salesitem->sequence)->get();
        if($remains){
            foreach($remains as $remain){
                $remain->sequence -= 1;
                $remain->save();
            }
        }
        return Redirect::action('MarketingController@indexSetup');

    }

    // PRIVATE MEHTODS
    // method for deleting deals upon transaction deletion
    private function dealDeleteMultiple($transaction_id)
    {
        $deals = Deal::where('transaction_id', $transaction_id)->get();
        foreach($deals as $deal){
            $item = Item::findOrFail($deal->item_id);
            if($deal->qty_status == '1'){
                $deal->qty_status = 3;
                $deal->save();
            }else if($deal->qty_status == '2'){
                $item->qty_now += $deal->qty;
                $deal->qty_status = 3;
                $deal->save();
            }
            $item->save();
            $this->dealSyncOrder($item->id);
        }
    }

    // method for reverting deals upon undo delete
    private function dealUndoDelete($transaction_id)
    {
        $deals = Deal::where('transaction_id', $transaction_id)->where('qty_status', '3')->get();
        $transaction = Transaction::findOrFail($transaction_id);

        if($transaction->cancel_trace === 'Confirmed'){
            foreach($deals as $deal){
                $item = Item::findOrFail($deal->item_id);
                $deal->qty_status = 1;
                $deal->save();
                $this->dealSyncOrder($item->id);
            }
        }else if($transaction->cancel_trace === 'Delivered' or $transaction->cancel_trace === 'Verified Owe' or $transaction->cancel_trace === 'Verified Paid'){
            foreach($deals as $deal){
                $item = Item::findOrFail($deal->item_id);
                $deal->qty_status = 2;
                $deal->save();
                $item->qty_now -= $deal->qty;
                $item->save();
            }
        }
    }

    // create person with initial D
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

    // password reset functionality (dtd)
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

    // calculating gst and non for deal total
    private function calDealTotal($query)
    {
        $query_deal = clone $query;
        $query_deal = $query_deal->whereHas('dtddeals.item', function($q){
            $q->whereNotIn('product_id', [301, 302]);
        });
        $total_amount = 0;
        $nonGst_amount = 0;
        $gst_amount = 0;
        $query1 = clone $query_deal;
        $query2 = clone $query_deal;

        $nonGst_amount = $query1->whereHas('person.profile', function($query1){
                            $query1->where('gst', 0);
                        })->sum(DB::raw('ROUND(total, 2)'));

        $gst_amount = $query2->whereHas('person.profile', function($query2){
                        $query2->where('gst', 1);
                    })->sum(DB::raw('ROUND((total * 107/100), 2)'));

        $total_amount = $nonGst_amount + $gst_amount;
        return $total_amount;
    }

    // calculating gst and non for comm total
    private function calCommTotal($query)
    {
        $query_comm = clone $query;
        $query_comm = $query_comm->whereHas('dtddeals.item', function($q){
            $q->whereIn('product_id', [301, 302]);
        });
        $total_amount = 0;
        $nonGst_amount = 0;
        $gst_amount = 0;
        $query3 = clone $query_comm;
        $query4 = clone $query_comm;

        $nonGst_amount = $query3->whereHas('person.profile', function($query3){
                            $query3->where('gst', 0);
                        })->sum(DB::raw('ROUND(total, 2)'));

        $gst_amount = $query4->whereHas('person.profile', function($query4){
                        $query4->where('gst', 1);
                    })->sum(DB::raw('ROUND((total * 107/100), 2)'));

        $total_amount = $nonGst_amount + $gst_amount;
        return abs($total_amount);
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
                            $dtddeal->dividend = strstr($qty, '/') ? strstr($qty, '/', true) : $qty;
                            $dtddeal->divisor = strstr($qty, '/') ? substr($qty, strpos($qty, '/') + 1) : 1;
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
                        $dtddeal->dividend = strstr($qty, '/') ? strstr($qty, '/', true) : $qty;
                        $dtddeal->divisor = strstr($qty, '/') ? substr($qty, strpos($qty, '/') + 1) : 1;
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

    // create new Transaction or find back and update
    private function syncTransaction($dtdtransaction_id, $request)
    {
        $dtdtransaction = DtdTransaction::findOrFail($dtdtransaction_id);
        $person = Person::findOrFail($dtdtransaction->person_id);
        $request->merge(array('person_code' => $person->cust_id));

        if($dtdtransaction->transaction_id == null || $dtdtransaction->transaction_id == ''){
            $transaction = Transaction::create($request->all());
        }else{
            $transaction = Transaction::findOrFail($dtdtransaction->transaction_id);
            $transaction->update($request->all());
        }
        $transaction->total = $dtdtransaction->total;
        $transaction->total_qty = $dtdtransaction->total_qty;
        $transaction->dtdtransaction_id = $dtdtransaction->id;
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
                $deal->dividend = $dtddeal->dividend;
                $deal->divisor = $dtddeal->divisor;
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
                $this->dealSyncOrder($dealresult->item_id);
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
        $limit = false;
        if($item->lowest_limit) {
            if($item->qty_now - $item->qty_order - $qty < $item->lowest_limit) {
                $limit = true;
            }
        }else {
            $limit = true;
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

    // pass through to check the form valid for edit or not (single collection)
    private function checkFormEditable($transaction)
    {
        $noneditable = false;
/*
        // find person is belongs to dtd member
        if($transaction->person->cust_id[0] === 'D'){
            $noneditable = true;
        }else{
            return false;
        }

        // determine the current user is dtd embassador or not
        if(Person::whereUserId(Auth::user()->id)->first()){
            if(Person::whereUserId(Auth::user()->id)->first()->cust_type === 'AB'){
                $noneditable = true;
            }else{
                return false;
            }
        }else{
            return false;
        }

        // determine the current transaction status is confirmed
        if($transaction->status === 'Confirmed'){
            $noneditable = true;
        }else{
            return false;
        }

        // determine the delivery date is more than today
        if(Carbon::today() >= Carbon::parse($transaction->delivery_date)->subDay()){
            $noneditable = true;
        }else{
            return false;
        }*/
        return $noneditable;
    }

    // processing d2d sales items sequence number
    // based on entered sequence taking action rearrange number for creation(int $sequence_num) [int $running_num]
    public function createSequence($sequence_num)
    {
        $running_num = 0;
        $duplicate = D2dOnlineSale::whereSequence($sequence_num)->first();
        if($duplicate){
            $greater_numbers = D2dOnlineSale::where('sequence', '>=', $sequence_num)->get();
            foreach($greater_numbers as $greater_number){
                $greater_number->sequence = $greater_number->sequence + 1;
                $greater_number->save();
            }
            $running_num = $sequence_num;
        }else{
            $running_num = (int) D2dOnlineSale::max('sequence') + 1;
        }
        return $running_num;
    }
}
