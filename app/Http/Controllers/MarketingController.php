<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Requests\MemberRequest;
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

    public function indexCustomer()
    {
        return view('market.customer');
    }

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

            return $members->descendantsAndSelf()->reOrderBy('cust_type', 'desc')->get();

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

        // dd($request->all());
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

            $creator = Person::where('user_id', Auth::user()->id)->first();

            $person->makeChildOf($creator);

        }

        if($person){

            Flash::success('User Successfully Registered');

            return Redirect::action('MarketingController@indexMember');

        }else{

            Flash::error('Please Try Again');

            return Redirect::action('MarketingController@createMember', $request->level);
        }
/*
        }else{

            return view('market.member.create');
        }*/
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
