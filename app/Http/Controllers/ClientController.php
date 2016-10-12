<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

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
use App\Postcode;

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

    // return vending page
    public function vendingIndex()
    {
        return view('client.vending');
    }

    // return recruitment page
    public function recruitmentIndex()
    {
        return view('client.recruitment');
    }

    // return franchise page
    public function franchiseIndex()
    {
        return view('client.franchise');
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
        $lookupArr = [
            '1' => 'Red Bean Jelly (5pcs/ box)',
            '2' => 'Chocolate Pie with Mango (5pcs/ box)',
            '3' => 'QQ Pudding (5pcs/ box)',
            '4' => 'Green Mango & Lime (5pcs/ box)',
            '5' => 'Chocolate Roll (5pcs/ flavor)',
            '6' => 'Vanilla Roll (5pcs/ flavor)',
            '7' => 'Matcha Roll (5pcs/ flavor)',
            '8' => 'Strawberry (6pcs/ set)',
            '9' => 'Mint Chocolate (6pcs/ set)'
        ];

        $priceArr = [
            '1' => 7.90,
            '2' => 7.90,
            '3' => 7.90,
            '4' => 7.90,
            '5' => 8.50,
            '6' => 8.50,
            '7' => 8.50,
            '8' => 7.90,
            '9' => 9.50
        ];

        $dayArr = [
            '1' => 'Same Day',
            '2' => 'Within 1 Day',
            '3' => 'Within 2 Days',
        ];

        $timeArr = [
            '1' => '8am - 12pm',
            '2' => '12pm - 5pm',
            '3' => '5pm - 9pm',
        ];

        return view('client.d2d', compact('lookupArr', 'priceArr', 'dayArr', 'timeArr'));
    }

    public function emailOrder(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'contact' => 'required',
            'email' => 'required',
            'postcode' => 'required',
            'block' => 'required',
            'floor' => 'required',
            'unit' => 'required',
        ]);

        if($request->total == 0){
            Flash::error('Please choose something before proceed');
            return Redirect::action('ClientController@d2dIndex');
        }
/*
        if($request->email){
            $existing = Person::where('email', $request->email)->first();
            if(! $existing){
                // do the logic to store the customer based on postal code and auto assign AB
            }
        }*/

        $lookupArr = [
            '1' => 'Red Bean Jelly (5pcs/ box)',
            '2' => 'Chocolate Pie with Mango (5pcs/ box)',
            '3' => 'QQ Pudding (5pcs/ box)',
            '4' => 'Green Mango & Lime (5pcs/ box)',
            '5' => 'Chocolate Roll (5pcs/ flavor)',
            '6' => 'Vanilla Roll (5pcs/ flavor)',
            '7' => 'Matcha Roll (5pcs/ flavor)',
            '8' => 'Strawberry (6pcs/ set)',
            '9' => 'Mint Chocolate (6pcs/ set)'
        ];
        $dayArr = [
            '1' => 'Same Day',
            '2' => 'Within 1 Day',
            '3' => 'Within 2 Days',
        ];
        $timeArr = [
            '1' => '8am - 12pm',
            '2' => '12pm - 5pm',
            '3' => '5pm - 9pm',
        ];
/*
        $covered_custid = false;
        // trace the postcode is within the coverage
        if($request->postcode){
            $search_postcode = Postcode::whereValue($request->postcode)->first();
            if($search_postcode){
                if($search_postcode->person_id){
                    // create d2d customer (H) based on the online order postcode given
                    $covered_custid = $this->createDtdCustomer($search_postcode, $request);
                }
            }
        }
        // create dtdtransaction if the dtd online order is within the coverage
        if($covered_custid){

        }*/

        // email array send from
        $sendfrom = ['system@happyice.com.sg'];
        // email array send to
        $adminemails = User::whereHas('roles', function($q){
            $q->where('name', 'admin');
        })->get();

        if($adminemails){
            foreach($adminemails as $adminemail){
                if($adminemail->email){
                    $sendto[] = $adminemail->email;
                }
            }
            $sendto = array_unique($sendto);
        }else{
            $sendto = ['daniel.ma@happyice.com.sg'];
        }
        array_push($sendto, 'jiahaur91@hotmail.com');
        // $sendto = ['leehongjie91@gmail.com'];

        // capture email sending date
        $today = Carbon::now()->format('d-F-Y');
        $data = array(
            'name' => $request->name,
            'contact' => $request->contact,
            'email' => $request->email,
            'street' => $request->street,
            'postcode' => $request->postcode,
            'block' => $request->block,
            'floor' => $request->floor,
            'unit' => $request->unit,
            'remark' => $request->remark,
            'total' => $request->total,
            'itemArr' => $request->itemArr,
            'qtyArr' => $request->qtyArr,
            'amountArr' => $request->amountArr,
            'lookupArr' => $lookupArr,
            'timeslot' => $timeArr[$request->del_time],
            'dayslot' => $dayArr[$request->del_date],
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
        return Redirect::action('ClientController@d2dIndex');
    }

    // create H code customer process based on given postcode and assign to member
    private function createDtdCustomer($postcode_data, $request)
    {
        $existing_cust = Person::where('cust_id', 'LIKE', 'H%')->whereContact(preg_replace('/\D/', '', $request->contact))->first();
        $latest_id = Person::where('cust_id', 'LIKE', 'H%')->first() ? (int) substr(Person::where('cust_id', 'LIKE', 'H%')->max('cust_id'), 1) : (int) '100001';

        if(!$existing_cust){
            $new_cust = Person::create([
                'cust_id' => 'H'.($latest_id + 1),
                'name' => $request->name,
                'contact' => $request->contact,
                'email' => $request->email,
                'street' => $request->street,
                'del_postcode' => $request->postcode,
                'block' => $request->block,
                'floor' => $request->floor,
                'unit' => $request->unit,
                'profile_id' => 1,
                'payterm' => 'C.O.D',
            ]);
            return $new_cust->id;
        }else{
            return $existing_cust->id;
        }
    }
}
