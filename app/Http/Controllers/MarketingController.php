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
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\User;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexInvoice()
    {
        return view('market.deal');
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

        }else{

            $creator = Person::where('user_id', Auth::user()->id)->first();

            $person->makeChildOf($creator);

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
        $members = Person::where('user_id', Auth::user()->id)->first();

        if($members){

            return $members->descendants()->where('cust_id', 'LIKE', 'D%')->reOrderBy('cust_type', 'desc')->get();

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

        }else{

            if($latest_cust == 'D100001'){

                $person->makeRoot();

            }else{

                $creator = Person::where('user_id', Auth::user()->id)->first();

                $person->makeChildOf($creator);

            }
        }

        if($person){

            Flash::success('User Successfully Registered');

            return Redirect::action('MarketingController@indexMember');

        }else{

            Flash::error('Please Try Again');

            return Redirect::action('MarketingController@createMember', $request->level);
        }
    }

    public function updateSelf(Request $request, $self_id)
    {
        $input = $request->all();

        $person = Person::findOrFail($self_id);

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

    public function indexDocs()
    {
        return view('market.docs');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editMember($id)
    {
        $person = Person::findOrFail($id);

        return view('market.member.edit', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMember(Request $request, $id)
    {
        $input = $request->all();

        $person = Person::findOrFail($id);

        $user = User::findOrFail($person->user_id);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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

            // $user->assignRole('marketer');

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
}
