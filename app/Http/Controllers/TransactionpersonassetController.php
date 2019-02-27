<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Transactionpersonasset;
use App\Transaction;
use App\Deliveryorder;
use DB;

class TransactionpersonassetController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // retrieve transactionpersonasset index api(int transaction_id)
    public function indexApi($transaction_id)
    {
        $transactionpersonassets = DB::table('transactionpersonassets')
            ->leftJoin('personassets', 'personassets.id', '=', 'transactionpersonassets.personasset_id')
            ->where(function($query) use ($transaction_id) {
                $query->where('transactionpersonassets.transaction_id', $transaction_id)
                        ->orWhere('transactionpersonassets.to_transaction_id', $transaction_id);
            })
            ->select(
                'transactionpersonassets.id', 'transactionpersonassets.serial_no', 'transactionpersonassets.sticker',
                'transactionpersonassets.remarks', 'transactionpersonassets.qty',
                'personassets.code', 'personassets.name', 'personassets.brand'
            )
            ->oldest('transactionpersonassets.updated_at')
            ->get();


        $data = [
            'data' => $transactionpersonassets
        ];

        return $data;
    }

    // create transactionpersonasset api()
    public function createApi()
    {
        $personasset_id = request('personasset_id');
        $transaction_id = request('transaction_id');
        $transactionpersonasset_id = request('transactionpersonasset_id');
        $items = request('items');
        $transaction = Transaction::findOrFail($transaction_id);
        $deliveryorder = Deliveryorder::where('transaction_id', $transaction_id)->firstOrFail();

        if($transactionpersonasset_id) {
            $transactionpersonasset = Transactionpersonasset::findOrFail($transactionpersonasset_id);
            $transactionpersonasset->to_transaction_id = $transaction_id;
            $transactionpersonasset->save();
            $deliveryorder->from_happyice = 1;
            $deliveryorder->save();

            if($transaction->status != 'Pending' and $transaction->status != 'Confirmed' and $transaction->status != 'Cancelled') {
                $transactionpersonasset->dateout = $transaction->deliveryorder->pickup_date;
                $transactionpersonasset->save();
            }
        }else {
            foreach ($items as $item) {
                $transactionpersonasset = Transactionpersonasset::create([
                    'personasset_id' => $personasset_id,
                    'transaction_id' => $transaction_id,
                    'serial_no' => $item['serial_no'],
                    'sticker' => $item['sticker'],
                    'remarks' => $item['remarks'],
                    'qty' => 1
                ]);

                if ($transaction->status != 'Pending' and $transaction->status != 'Confirmed' and $transaction->status != 'Cancelled') {
                    $transactionpersonasset->datein = $transaction->deliveryorder->pickup_date;
                    $transactionpersonasset->is_warehouse = 1;
                    $transactionpersonasset->save();
                }
            }
            $deliveryorder->to_happyice = 1;
            $deliveryorder->save();
        }
    }

    // delete transactionpersonasset entry based on id(int id)
    public function destroyApi($id)
    {
        $transactionpersonasset = Transactionpersonasset::findOrFail($id);
        // die(var_dump( $transactionpersonasset->toArray()));
        $deliveryorder = Deliveryorder::where('transaction_id', $transactionpersonasset->to_transaction_id)->first();
        if($deliveryorder) {
            // die(var_dump('here1'));
            $transactionpersonasset->to_transaction_id = '';
            $transactionpersonasset->save();
        }else {
            // die(var_dump('here2'));
            $transactionpersonasset->delete();
        }
    }

    // update transactionpersonasset api()
    public function updateApi()
    {
        $transactionpersonasset = Transactionpersonasset::findOrFail(request('id'));

        $transactionpersonasset->update([
/*             'person_id' => request('person_id'),
            'name' => request('name'),
            'code' => request('code'),
            'brand' => request('brand'), */
            'personasset_id' => request('personasset_id'),
            'serial_no' => request('serial_no'),
            'sticker' => request('sticker'),
            'remarks' => request('remarks')
        ]);
    }
}
