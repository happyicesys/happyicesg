<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Person;
use Auth;
use Laracasts\Flash\Flash;
use Carbon\Carbon;

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
        return view('market.member.index');
    }

    public function indexMemberApi()
    {
        $members = Person::where('user_id', Auth::user()->id)->first();

        if($members){

            return $members->getDescendantsAndSelf();

        }else{

            return '';
        }
    }

    public function createMember()
    {
        return view('market.member.create');
    }

    public function storeMember(Request $request)
    {
        if($this->createUser($request)){

            $people = Person::where('cust_id', 'LIKE', 'D%')->get();

            if(count($people) > 0){

                $latest_cust = $people->max('cust_id') + 1;

            }else{

                $latest_cust = 'D100001';
            }


            $request->merge(array('cust_id' => $latest_cust));

            $request->merge(array('profile_id' => 1));

            $input = $request->all();

            $person = Person::create($input);

            if($person){

                Flash::success('User Successfully Registered');

                return view('market.member.index');

            }else{

                Flash::error('Please Try Again');

                return view('market.member.create');
            }

        }else{

            return view('market.member.create');
        }
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

            return true;

        }else{

            Flash::error('Please fill up the email');

            return false;
        }
    }

    // user get the credentials via email
    private function sendEmailUponRegistration($request, $password)
    {

        $today = Carbon::now()->format('d-m-Y H:i');

        $email = $request->email;

        // $sender = 'daniel.ma@happyice.com.sg';
        $sender = 'system@happyice.com.sg';

        $data = [

            'username' => $request->company,
            'password' => $password,
            'url' => 'http://www.happyice.com.sg/admin',

        ];

        Mail::send('email.marketing_registration', $data, function ($message) use ($email, $today, $sender)
        {
            $message->from($sender);
            $message->subject('Thanks for Your Registration (Door To Door Project) - '.$today);
            $message->setTo($email);
        });
    }
}
