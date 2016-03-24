<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ClientRegisterRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Item;
use App\Person;
use App\User;
use App\Profile;
use App\Role;

class ClientController extends Controller
{
    // publish products from the items
    public function clientProduct()
    {
        $items = Item::wherePublish(1)->get();

        return $items;
    }

    // return ecommerce register page
    public function getRegister()
    {
        return view('client.register');
    }

    public function store(ClientRegisterRequest $request)
    {
        // find out the latest ecommerce id for incrementation ExxxxxL
        $latest_custid = Person::where('cust_id', 'LIKE', 'E%L')->max('cust_id');

        $cust_id = preg_replace("/[^0-9]/", "", $latest_custid) + 1;

        // replace the person attributes
        $request->merge(array('cust_id' => 'E'.$cust_id.'L'));

        $request->merge(array('username' => strtolower(preg_replace('/\s+/', '', $request->name))));

        $request->merge(array('company' => $request->name));

        $request->merge(array('bill_address' => $request->del_address));

        $request->merge(array('profile_id' => Profile::whereGst(0)->first()->id));

        $request->merge(array('cust_id' => 'E'.$cust_id.'L'));

        $input = $request->all();

        $person = Person::create($input);

        $user = User::create($input);

        $user->roles()->attach(Role::whereName('ecommerce')->firstOrFail()->id);

        // login the user right after registration
        Auth::login($user);

        // redirect user to the product page
        return redirect('user');
    }

}
