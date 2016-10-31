<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\D2dOnlineSale;
use App\EmailAlert;
use App\Person;
use App\Deal;
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
        dd($request->all());
        $generate_trans = false;
        $avail_postcode = Postcode::whereValue($request->postcode)->first();
        $sendto = array();

        // validate whether the postcode is available or not
        if($avail_postcode) {
            $generate_trans = $avail_postcode->person_id ? false : true;
        }else{
            $generate_trans = true;
        }

        // sync existing customer or create new one based on unique contact number
        $this->syncCustomer($request);

        if($generate_trans) {
            $this->createTransaction($request);
        }else{
            // find out whether postcode bind to a person_id or not and take action
            if($avail_postcode->person_id) {

            }else {

            }
        }
        $data = [
            'idArr' => $request->idArr,
            'captionArr' => $request->captionArr,
            'qtyArr' => $request->qtyArr,
            'amountArr' => $request->amountArr,
            'total' => $request->total,
            'totalqty' => $request->totalqty,
            'delivery' => $request->$delivery,
        ];

        foreach($qtyArr as $index => $qty) {
            if($qty != '' and $qty != null and $qty != 0) {

            }
        }
    }

    // generate new customer upon submitting the order - H code unique contact number(FormRequest request)
    private function syncCustomer($request)
    {
        $customer = Person::where('cust_id', 'LIKE', 'H%')->where('contact', $request->contact)->orWhere('alt_contact', $request->contact)->first();
        if(!$customer) {
            $customer = new Person();
            $customer->cust_id = $this->getCustRunningNum();
            $customer->name = $request->name;
            $customer->contact = $request->contact;
            $customer->email = $request->email;
            $customer->del_postcode = $request->postcode;
            $customer->block = $request->block;
            $customer->floor = $request->floor;
            $customer->unit = $requet->unit;
            $customer->save();
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

    // create transaction upon customer submit order [given the postcode is not found or not bind to person_id](FormRequest request)
    private function createTransaction($request)
    {
        $transaction = new Transaction();
        $transaction->updated_by = 'D2d System';
        $transaction->delivery_date = Carbon::today();
        $transaction->order_date = Carbon::today();
        $transaction->status = 'Confirmed';
        $transaction->total = $request->total;
        $transaction->total_qty = $request->totalqty;
        $transaction->remark = $request->delivery ? 'D2d - Delivery charge: '.$request->delivery : 'D2d';
        $transaction->save();
        $this->createDeals($request, $transaction->id);
    }

    // create deals (ForrmRequest request, integer transaction_id)
    private function createDeals($request, $transaction_id)
    {
        $idArr = $request->idArr;
        $qtyArr = $request->qtyArr;
        $amountArr = $request->amountArr;

        if(array_filter($qtyArr) != null) {
            foreach($qtyArr as $index => $qty) {
                if($qty != null or $qty != '' or $qty != 0) {
                    $onlinesaleitem = D2dOnlineSale::findOrFail($idArr[$index]);
                    $item = Item::findOrFail($onlinesaleitem->id);
                    if($item->email_limit) {
                        if($this->calOrderEmailLimit($qty, $item)) {
                            if(!$item->emailed) {
                                $this->sendEmailAlert($item);
                                $item->emailed = true;
                                $item->save();
                            }
                        }else {
                            $item->emailed = false;
                            $item->save();
                        }
                    }

                    $deal = new Deal();
                    $deal->transaction_id = $transaction_id;
                    $deal->item_id = $item->id;
                    $deal->qty = $qty;
                    $deal->amount = $amountArr[$index];
                    $deal->unit_price = $quotes[$index];
                    $deal->qty_status = $status;
                    $deal->save();
                    $this->dealSyncOrder($index);
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
}
