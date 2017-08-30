<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\Http\Requests;
use App\D2dOnlineSale;
use App\EmailAlert;
use App\Person;
use App\Deal;
use App\Postcode;
use App\Transaction;
use App\DtdTransaction;
use App\Custcategory;
use App\DtdDeal;
use App\Price;
use App\Item;
use App\Unitcost;
use DB;

class D2dOnlineSaleController extends Controller
{
    // return all data in d2donlinesales item
    public function allApi()
    {
        $salesitems = DB::table('d2d_online_sales')
                        ->leftJoin('people', 'd2d_online_sales.person_id', '=', 'people.id')
                        ->leftJoin('items', 'd2d_online_sales.item_id', '=', 'items.id')
                        ->leftJoin('prices', function($join) {
                            $join->on('prices.person_id', '=', 'people.id')
                                    ->on('prices.item_id', '=', 'items.id');
                        })
                        ->select(
                            'people.id as person_id', 'people.cust_id as cust_id', 'items.name as item_name',
                            'items.product_id', 'd2d_online_sales.id', 'd2d_online_sales.caption',
                            'd2d_online_sales.qty_divisor', 'prices.quote_price', 'd2d_online_sales.coverage'
                            )
                        ->orderBy('sequence')
                        ->get();
        // $salesitems = D2dOnlineSale::with(['item', 'person', 'person.prices'])->orderBy('sequence')->get();
        return $salesitems;
    }

    // showing d2d sales items
    public function allItems($covered)
    {
        $covered = $covered == 'true' ? 'within' : 'without';
        $salesitems = DB::table('d2d_online_sales')
                        ->leftJoin('people', 'd2d_online_sales.person_id', '=', 'people.id')
                        ->leftJoin('items', 'd2d_online_sales.item_id', '=', 'items.id')
                        ->leftJoin('prices', function($join) {
                            $join->on('prices.person_id', '=', 'people.id')
                                    ->on('prices.item_id', '=', 'items.id');
                        })
                        ->whereIn('d2d_online_sales.coverage', ['all', $covered])
                        ->select(
                            'people.id as person_id', 'people.cust_id as cust_id', 'items.name as item_name',
                            'items.product_id', 'd2d_online_sales.id', 'd2d_online_sales.caption',
                            'd2d_online_sales.qty_divisor', 'prices.quote_price'
                            )
                        ->orderBy('sequence')
                        ->get();
        // $salesitems = D2dOnlineSale::with(['item', 'person', 'person.prices'])->orderBy('sequence')->get();
        return $salesitems;
    }

    // proceed d2d online order form
    public function submitOrder(Request $request)
    {
        $this->validate($request, [
            'my_name'   => 'honeypot',
            'my_time'   => 'required|honeytime:10'
        ]);

        $generate_trans = false;
        $avail_postcode = Postcode::whereValue($request->postcode)->first();
        $sendfrom = 'system@happyice.com.sg';
        $sendto = array();
        $cc = array();
        $transaction_id = '';
        $dtdtransaction_id = '';
        $today = Carbon::now()->format('d-F-Y');

        $date_options = [
            1 => 'Within 1 Day',
            2 => 'Within 2 Days'
        ];

        $time_options = [
            1 => '8am - 12pm',
            2 => '12pm - 5pm',
            3 => '5pm - 9pm'
        ];

        $request->merge(array('del_date' => $date_options[$request->del_date]));
        $request->merge(array('del_time' => $time_options[$request->del_time]));

        // validate whether the postcode is available or not
        if($avail_postcode) {
            $generate_trans = $avail_postcode->person_id ? false : true;
        }else{
            $generate_trans = true;
        }
        // sync existing customer or create new one based on unique contact number
        $customer_id = $this->syncCustomer($request, $avail_postcode);
        $sendto = [$request->email];
        if($generate_trans) {
            $transaction_id = $this->createTransaction($request, $customer_id);
            $cc = ['daniel.ma@happyice.com.sg', 'kent@happyice.com.sg', 'jhhappyice@gmail.com'];
            $bcc = ['leehongjie91@gmail.com'];
        }else{
            $member = Person::findOrFail($avail_postcode->person_id);
            $member_manager = $member->parent_id ? Person::find($member->parent_id)->first() : '';
            if($member_manager != null and $member_manager != '') {
                $cc = $member_manager->email != null ? [$member->email, $member_manager->email] : [$member->email];
            }else{
                $cc = [$member->email];
            }
            $bcc = ['daniel.ma@happyice.com.sg', 'kent@happyice.com.sg', 'leehongjie91@gmail.com', 'jhhappyice@gmail.com'];
            $dtdtransaction_id = $this->createDtdTransaction($request, $customer_id);
        }
        $data = [
            'idArr' => $request->idArr,
            'captionArr' => $request->captionArr,
            'qtyArr' => $request->qtyArr,
            'amountArr' => $request->amountArr,
            'total' => $request->total,
            'totalqty' => $request->totalqty,
            'delivery' => $request->delivery,
            'person' => Person::findOrFail($customer_id),
            'name' => $request->name,
            'email' => $request->email,
            'street' => $request->street,
            'postcode' => $request->postcode,
            'block' => $request->block,
            'floor' => $request->floor,
            'unit' => $request->unit,
            'transaction' => $transaction_id ? Transaction::findOrFail($transaction_id) : null,
            'dtdtransaction' => $dtdtransaction_id ? DtdTransaction::findOrFail($dtdtransaction_id) : null,
            'timing' => $request->del_date.'; '.$request->del_time,
            'remark' => $request->remark,
        ];
        if(count($sendto) > 0) {
            Mail::send('email.submit_order', $data, function ($message) use ($sendfrom, $sendto, $cc, $bcc, $today){
                $message->from($sendfrom);
                $message->cc($cc);
                // $message->cc('leehongjie91@gmail.com');
                if(isset($bcc) and $bcc != '') {
                    $message->bcc($bcc);
                }
                $message->subject('HappyIce - Thanks for purchase ['.$today.']');
                $message->setTo($sendto);
                // $message->setTo('leehongjie91@gmail.com');
            });
        }
        return view('client.order_confirmed', compact('today', 'data'));
    }

    // validate order via vue resource
    public function validateOrder(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'email',
            'postcode' => 'required|digits:6',
            'contact' => 'required|regex:/^[89]\d{7}$/',
            'block' => 'required',
            'floor' => 'required',
            'unit' => 'required',
        ], [
            'name.required' => 'Please fill in the name',
            'email.email' => 'The email format is not right',
            'postcode.required' => 'Please fill in the postcode',
            'postcode.digits' => 'The postcode format is not right',
            'contact.required' => 'Please fill in the handphone number',
            'contact.regex' => 'The handphone number can only contain 8 numbers and started with 8 or 9',
            'block.required' => 'Please fill in the block',
            'floor.required' => 'Please fill in the floor',
            'unit.required' => 'Please fill in the unit',
        ]);
    }

    // generate new customer upon submitting the order - H code unique contact number(FormRequest request)
    private function syncCustomer($request, $avail_postcode)
    {
        $contact = $request->contact;
        $customer = Person::where('cust_id', 'LIKE', 'H%')
                            ->whereNotNull('contact')
                            ->where('contact', '!=', '')
                            ->where('contact', $contact)
                            ->first();
        if(!$customer) {
            $cust_category = Custcategory::whereName('H')->first();
            $customer = new Person();
            $customer->cust_id = $this->getCustRunningNum();
            $customer->name = $request->name;
            $customer->company = $request->name;
            $customer->contact = $request->contact;
            $customer->email = $request->email;
            $customer->bill_address = $request->block.', #'.$request->floor.'-'.$request->unit.', '.$request->street;
            $customer->del_address = $request->block.', #'.$request->floor.'-'.$request->unit.', '.$request->street;
            $customer->del_postcode = $request->postcode;
            $customer->block = $request->block;
            $customer->floor = $request->floor;
            $customer->unit = $request->unit;
            $customer->profile_id = 1;
            if($cust_category) {
                $customer->custcategory_id = $cust_category->id;
            }
            $customer->save();
            if($avail_postcode){
                $manager = Person::findOrFail($avail_postcode->person_id);
                $customer->parent_name = $manager->name;
                $customer->makeChildOf($manager);
            }
        }
        return $customer->id;
    }

    // get H code customer running number
    private function getCustRunningNum()
    {
        $people = Person::withTrashed()->where('cust_id', 'LIKE', 'H%');
        $first_person = Person::where('cust_id', 'H100001')->first();
        if(count($people) > 0 and $first_person){
            $latest_cust = (int) substr($people->max('cust_id'), 1) + 1;
            $latest_cust = 'H'.$latest_cust;
        }else{
            $latest_cust = 'H100001';
        }
        return $latest_cust;
    }

    // create transaction upon customer submit order [given the postcode is not found or not bind to person_id](FormRequest request, int customer_id)
    private function createTransaction($request, $customer_id)
    {
        $cutoff_time = Carbon::createFromTime(22, 30, 0, 'Asia/Singapore');
        $person = Person::findOrFail($customer_id);
        $transaction = new Transaction();
        $transaction->updated_by = 'D2D System';
        $transaction->delivery_date = Carbon::now() >= $cutoff_time ? Carbon::today()->addDay() : Carbon::today();
        $transaction->order_date = Carbon::today();
        $transaction->status = 'Confirmed';
        $transaction->total = $request->total;
        $transaction->total_qty = $request->totalqty;
        $transaction->transremark = $request->del_date.'; '.$request->del_time;
        $transaction->person_id = $person->id;
        $transaction->person_code = $person->cust_id;
        $transaction->delivery_fee = $request->delivery;
        $transaction->del_address = $request->block.', #'.$request->floor.'-'.$request->unit.', '.$request->street;
        $transaction->bill_address = $request->block.', #'.$request->floor.'-'.$request->unit.', '.$request->street;
        $transaction->del_postcode = $request->postcode;
        $transaction->name = $request->name;
        $transaction->transremark = $request->del_date.'; '.$request->del_time.'; '.$request->remark;
        $transaction->save();
        $this->createDeals($request, $transaction->id);
        return $transaction->id;
    }

    // create deals (ForrmRequest request, integer transaction_id)
    private function createDeals($request, $transaction_id)
    {
        $idArr = $request->idArr;
        $qtyArr = $request->qtyArr;
        $amountArr = $request->amountArr;
        if(array_filter($qtyArr) != null) {
            foreach($qtyArr as $index => $qty) {
                if($qty != null and $qty != '' and $qty != 0) {
                    $onlinesaleitem = D2dOnlineSale::findOrFail($idArr[$index]);
                    $item = Item::findOrFail($onlinesaleitem->item_id);
                    $unitcost = Unitcost::whereItemId($item->id)->whereProfileId(Person::find(1643)->profile->id)->first();
                    $price = Price::wherePersonId(1643)->whereItemId($item->id)->first();
                    $deal = new Deal();
                    $deal->transaction_id = $transaction_id;
                    $deal->item_id = $item->id;
                    $deal->dividend = $qty;
                    $deal->divisor = $onlinesaleitem->qty_divisor;
                    $deal->qty = $qty / $onlinesaleitem->qty_divisor;
                    $deal->amount = $amountArr[$index];
                    $deal->unit_price = $price->quote_price;
                    $deal->qty_status = 1;
                    if($unitcost) {
                        $deal->unit_cost = $unitcost->unit_cost;
                    }
                    $deal->save();
                    $this->dealSyncOrder($item->id);
                }
            }
        }
    }

    // calculate the email order limit
    private function calOrderEmailLimit($qty, $item)
    {
        if(($item->qty_now - $item->qty_order - $qty < $item->email_limit) and ($qty > 0)){
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

    // sync confirmed deal status 1
    private function dealSyncOrder($item_id)
    {
        $deals = Deal::where('qty_status', '1')->where('item_id', $item_id);
        $item = Item::findOrFail($item_id);
        $item->qty_order = $deals->sum('qty');
        $item->save();
    }

    // create d2dtransaction for H code within coverage(FormRequest, int $person_id)
    private function createDtdTransaction($request, $person_id)
    {
        $cutoff_time = Carbon::createFromTime(20, 30, 0, 'Asia/Singapore');
        $person = Person::findOrFail($person_id);
        $dtdtransaction = new DtdTransaction();
        $dtdtransaction->updated_by = 'D2D System';
        $dtdtransaction->delivery_date = Carbon::now() >= $cutoff_time ? Carbon::today()->addDay() : Carbon::today();
        $dtdtransaction->order_date = Carbon::today();
        $dtdtransaction->status = 'Confirmed';
        $dtdtransaction->total = $request->total;
        $dtdtransaction->total_qty = $request->totalqty;
        $dtdtransaction->transremark = $request->del_date.'; '.$request->del_time;
        $dtdtransaction->person_id = $person->id;
        $dtdtransaction->person_code = $person->cust_id;
        $dtdtransaction->delivery_fee = $request->delivery;
        $dtdtransaction->del_address = $request->block.', #'.$request->floor.'-'.$request->unit.', '.$request->street;
        $dtdtransaction->bill_address = $request->block.', #'.$request->floor.'-'.$request->unit.', '.$request->street;
        $dtdtransaction->name = $request->name;
        $dtdtransaction->save();
        $this->createDtdDeals($request, $dtdtransaction->id);
        return $dtdtransaction->id;
    }

    // create DtdDeals for online order within coverage(FormRequest $request, int $dtdtransaction_id)
    private function createDtdDeals($request, $dtdtransaction_id)
    {
        $idArr = $request->idArr;
        $qtyArr = $request->qtyArr;
        $amountArr = $request->amountArr;
        if(array_filter($qtyArr) != null) {
            foreach($qtyArr as $index => $qty) {
                if($qty != null and $qty != '' and $qty != 0) {
                    $onlinesaleitem = D2dOnlineSale::findOrFail($idArr[$index]);
                    $item = Item::findOrFail($onlinesaleitem->item_id);
                    $price = Price::wherePersonId(1643)->whereItemId($item->id)->first();
                    $unitcost = Unitcost::whereProfileId(Person::find(1643)->profile->id)->whereItemId($item->id)->first();
                    $deal = new DtdDeal();
                    $deal->transaction_id = $dtdtransaction_id;
                    $deal->item_id = $item->id;
                    $deal->dividend = $qty;
                    $deal->divisor = $onlinesaleitem->qty_divisor;
                    $deal->qty = $qty / $onlinesaleitem->qty_divisor;
                    $deal->amount = $amountArr[$index];
                    $deal->unit_price = $price->quote_price;
                    $deal->qty_status = 1;
                    if($unitcost) {
                        $deal->unit_cost = $unitcost->unit_cost;
                    }
                    $deal->save();
                }
            }
        }
    }
}
