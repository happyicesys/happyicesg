<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests\ContactFormRequest;
use App\Http\Requests\ClientRegisterRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use DB;
use Auth;
use App\Item;
use App\Person;
use App\User;
use App\Profile;
use App\Role;
use App\DtdPrice;

class ClientController extends Controller
{
    // publish products from the items
    public function clientProduct()
    {
        $items = Item::wherePublish(1)->orderBy('product_id', 'asc')->get();

        return $items;
    }

    // return ecommerce register page
    public function getRegister()
    {
        return view('client.register');
    }

    // return ecommerce about us page
    public function getAboutUs()
    {
        return view('client.about');
    }

    // return product page
    public function getProduct()
    {
        return view('client.product');
    }

    // return product page
    public function getContact()
    {
        return view('client.contact');
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

    public function sendContactEmail(ContactFormRequest $request)
    {

        // email array send from
        $sendfrom = ['daniel.ma@happyice.com.sg'];

        // email array send to
        $sendto = ['daniel.ma@happyice.com.sg'];
        // $sendto = ['leehongjie91@gmail.com'];

        // capture email sending date
        $today = Carbon::now()->format('d-F-Y');

        $data = array(

            'name' => $request->name,

            'email' => $request->email,

            'contact' => $request->contact,

            'subject' => $request->subject,

            'bodymessage' => $request->message,
        );

        $mail =  Mail::send('client.email_contact', $data, function ($message) use ($sendfrom, $sendto, $today){

                    $message->from($sendfrom);

                    $message->subject('Contact Form Submission ['.$today.']');

                    $message->setTo($sendto);
        });

        if($mail){

            Flash::success('The form has been submitted');

        }else{

            Flash::error('Please Try Again');

        }

        return view('client.index');
    }

    public function d2dIndex()
    {
        return view('client.d2d');
    }

    public function emailOrder(Request $request)
    {
        $lookupArr = [
            '1' => 'Red Bean Jelly (box/5pcs)',
            '2' => 'Chocolate Pie with Mango (box/5pcs)',
            '3' => 'QQ Pudding (box/5pcs)',
            '4' => 'Green Mango & Lime (box/5pcs)',
            '5' => 'Chocolate Roll (flavor/5pcs)',
            '6' => 'Vanilla Roll (flavor/5pcs)',
            '7' => 'Matcha Roll (flavor/5pcs)',
            '8' => 'Strawberry (set/6pcs)',
            '9' => 'Mint Chocolate (set/6pcs)'
        ];

        // email array send from
        $sendfrom = ['system@happyice.com.sg'];

        // email array send to
        // $sendto = ['daniel.ma@happyice.com.sg'];
        $sendto = ['leehongjie91@gmail.com'];

        // capture email sending date
        $today = Carbon::now()->format('d-F-Y');

        $data = array(

            'name' => $request->name,

            'contact' => $request->contact,

            'email' => $request->email,

            'postcode' => $request->postcode,

            'block' => $request->block,

            'floor' => $request->floor,

            'unit' => $request->unit,

            'total' => $request->total,

            'itemArr' => $request->itemArr,

            'qtyArr' => $request->qtyArr,

            'amountArr' => $request->amountArr,

            'lookupArr' => $lookupArr,
        );

        $mail =  Mail::send('client.email_order', $data, function ($message) use ($sendfrom, $sendto, $today){

                    $message->from($sendfrom);

                    $message->subject('D2D Online Order Form ['.$today.']');

                    $message->setTo($sendto);
        });

        if($mail){

            Flash::success('The order has been submitted');

        }else{

            Flash::error('Please Try Again');

        }

        return view('client.index');
    }

}
