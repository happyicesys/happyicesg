<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Personasset;
use App\Transactionpersonasset;
use Carbon\Carbon;
use DB;

class PersonassetController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // retrieve index page()
    public function index()
    {
        return view('personasset.index');
    }

    // retrieve personasset index api()
    public function indexApi()
    {
        $person_id = request('person_id');
        $code = request('code');
        $name = request('name');
        $brand = request('brand');

        $personassets = Personasset::with(['person']);
        $personassets = DB::table('personassets')
            ->leftJoin('people', 'people.id', '=', 'personassets.person_id')
            ->select(
                'personassets.code', 'personassets.name', 'personassets.brand', 'personassets.id', 'personassets.id AS personasset_id',
                'personassets.size1', 'personassets.size2', 'personassets.weight', 'personassets.capacity', 'personassets.specs1',
                'personassets.specs2', 'personassets.specs3', 'personassets.person_id', 'people.cust_id', 'people.company'
            );

        // reading whether search input is filled
        if($person_id) {
            $personassets = $personassets->where( 'personassets.person_id', $person_id);
        }

        if($code) {
            $personassets = $personassets->where( 'personassets.code', 'LIKE', '%'.$code.'%');
        }

        if($name) {
            $personassets = $personassets->where( 'personassets.name', 'LIKE', '%' . $name . '%');
        }

        if($brand) {
            $personassets = $personassets->where( 'personassets.brand', 'LIKE', '%' . $brand . '%');
        }

        if (request('sortName')) {
            $personassets = $personassets->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $personassets = $personassets->latest('personassets.created_at');
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if ($pageNum == 'All') {
            $personassets = $personassets->get();
        } else {
            $personassets = $personassets->paginate($pageNum);
        }

        $data = [
            'data' => $personassets
        ];

        return $data;
    }

    // create asset api()
    public function createApi()
    {
        $personasset = Personasset::create([
            'person_id' => request('person_id') ? : 3301,
            'name' => request('name'),
            'code' => request('code'),
            'brand' => request('brand'),
            'size1' => request('size1'),
            'size2' => request('size2'),
            'weight' => request('weight'),
            'capacity' => request('capacity'),
            'specs1' => request('specs1'),
            'specs2' => request('specs2'),
            'specs3' => request('specs3'),
            'created_by' => auth()->user()->id,
            'created_at' => Carbon::parse(request('created_at'))
        ]);
    }

    // update customer asset api()
    public function updateApi()
    {
        $personasset = Personasset::findOrFail(request('id'));

        $personasset->update([
            'person_id' => request('person_id'),
            'name' => request('name'),
            'code' => request('code'),
            'brand' => request('brand'),
            'size1' => request('size1'),
            'size2' => request('size2'),
            'weight' => request('weight'),
            'capacity' => request('capacity'),
            'specs1' => request('specs1'),
            'specs2' => request('specs2'),
            'specs3' => request('specs3'),
            'created_by' => auth()->user()->id,
            'created_at' => Carbon::parse(request('created_at'))
        ]);
    }

    // delete person asset entry based on id(int id)
    public function destroyApi($id)
    {
        $personasset = Personasset::findOrFail($id);
        $personasset->delete();
    }

    public function destroyMovementApi($id)
    {
        $transactionpersonasset = Transactionpersonasset::findOrFail($id);
        $transactionpersonasset->delete();
    }

    // retrieve api for person asset movement()
    public function indexMovementApi()
    {
        // $year = request('year');
        // $week = request('week');
        $datefrom = request('datefrom');
        $dateto = request('dateto');
        $code = request('code');
        $name = request('name');
        $brand = request('brand');
        $serial_no = request('serial_no');
        $from_location = request('from_location');
        $from_invoice = request('from_invoice');
        $to_location = request('to_location');
        $to_invoice = request('to_invoice');

        $items = DB::table('transactionpersonassets')
            ->leftJoin('transactions', 'transactions.id', '=', 'transactionpersonassets.transaction_id')
            ->leftJoin('transactions AS to_transactions', 'to_transactions.id', '=', 'transactionpersonassets.to_transaction_id')
            ->leftJoin('deliveryorders', 'deliveryorders.transaction_id', '=', 'transactions.id')
            ->leftJoin('deliveryorders AS to_deliveryorders', 'to_deliveryorders.transaction_id', '=', 'to_transactions.id')
            ->leftJoin('personassets', 'personassets.id', '=', 'transactionpersonassets.personasset_id')
            ->select(
                'transactionpersonassets.id AS id', 'transactionpersonassets.transaction_id', 'transactionpersonassets.personasset_id',
                'transactionpersonassets.serial_no', 'transactionpersonassets.sticker', 'transactionpersonassets.remarks',
                DB::raw(
                'CASE WHEN to_transactions.status="Delivered" THEN to_transactions.id
                            WHEN to_transactions.status="Verified Owe" THEN to_transactions.id
                            WHEN to_transactions.status="Verified Paid" THEN to_transactions.id
                            ELSE "" END AS to_transaction_id'
                ),
                DB::raw(
                'CASE WHEN to_transactions.status="Delivered" THEN to_deliveryorders.delivery_location_name
                            WHEN to_transactions.status="Verified Owe" THEN to_deliveryorders.delivery_location_name
                            WHEN to_transactions.status="Verified Paid" THEN to_deliveryorders.delivery_location_name
                            ELSE "" END AS to_location_name'
                ),
                DB::raw('DATE(transactionpersonassets.datein) AS datein'),
                DB::raw('DATE(transactionpersonassets.dateout) AS dateout'),
                DB::raw('WEEK(transactionpersonassets.datein, 1) AS datein_week'),
                DB::raw('YEAR(transactionpersonassets.datein) AS datein_year'),
                DB::raw('WEEK(transactionpersonassets.dateout, 1) AS dateout_week'),
                DB::raw('YEAR(transactionpersonassets.dateout) AS dateout_year'),
                'personassets.code', 'personassets.name', 'personassets.brand', 'personassets.id AS personasset_id',
                'deliveryorders.pickup_address', 'deliveryorders.pickup_postcode',
                'deliveryorders.pickup_location_name AS from_location_name',
                'deliveryorders.delivery_location_name'
            );
            // ->whereNotNull('transactionpersonassets.datein');

        // reading whether search input is filled
        if ($datefrom) {
            $items = $items->where(function($query) use ($datefrom){
                $query->whereDate('transactionpersonassets.datein', '>=', $datefrom)
                    ->orWhereDate('transactionpersonassets.dateout', '>=', $datefrom);
            });
        }

        if ($dateto) {
            $items = $items->where(function ($query) use ($dateto) {
                $query->whereDate('transactionpersonassets.datein', '<=', $dateto)
                    ->orWhereDate('transactionpersonassets.dateout', '<=', $dateto);
            });
        }

        if($code) {
            $items = $items->where( 'personassets.code', 'LIKE', '%'.$code.'%');
        }

        if($name) {
            $items = $items->where('personassets.name', 'LIKE', '%'.$name.'%');
        }

        if($brand){
            $items = $items->where('personassets.brand', 'LIKE', '%'.$brand.'%');
        }

        if($serial_no){
             $items = $items->where('transactionpersonassets.serial_no', 'LIKE', '%'.$serial_no.'%');
        }

        if($from_location) {
            $items = $items->where('deliveryorders.pickup_location_name', 'LIKE', '%'.$from_location.'%');
        }

        if($from_invoice) {
            $items = $items->where('transactionpersonassets.transaction_id', 'LIKE', '%'.$from_invoice.'%');
        }

        if($to_location) {
            $items = $items->where('to_deliveryorders.delivery_location_name', 'LIKE', '%'.$to_location.'%');
        }

        if($to_invoice) {
            $items = $items->where('transactionpersonassets.to_transaction_id', 'LIKE', '%'.$to_invoice.'%');
        }

        if (request('sortName')) {
            $items = $items->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $items = $items->latest('deliveryorders.pickup_date');
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if ($pageNum == 'All') {
            $items = $items->get();
        } else {
            $items = $items->paginate($pageNum);
        }
        // dd($items->toArray());
        $data = [
            'data' => $items
        ];

        return $data;
    }

    // retrieve api for person asset current()
    public function indexCurrentApi()
    {
        $datefrom = request('datefrom');
        $dateto = request('dateto');
        $code = request('code');
        $name = request('name');
        $brand = request('brand');
        $serial_no = request('serial_no');
        $from_location = request('from_location');
        $from_invoice = request('from_invoice');

        $items = DB::table('transactionpersonassets')
            ->leftJoin('transactions', 'transactions.id', '=', 'transactionpersonassets.transaction_id')
            ->leftJoin('transactions AS to_transactions', 'to_transactions.id', '=', 'transactionpersonassets.to_transaction_id')
            ->leftJoin('deliveryorders', 'deliveryorders.transaction_id', '=', 'transactions.id')
            ->leftJoin('deliveryorders AS to_deliveryorders', 'to_deliveryorders.transaction_id', '=', 'to_transactions.id')
            ->leftJoin('personassets', 'personassets.id', '=', 'transactionpersonassets.personasset_id')
            ->select(
                'transactionpersonassets.id AS id',
                'transactionpersonassets.transaction_id',
                'transactionpersonassets.personasset_id',
                'transactionpersonassets.serial_no',
                'transactionpersonassets.sticker',
                'transactionpersonassets.remarks',
                DB::raw('DATE(transactionpersonassets.datein) AS datein'),
                DB::raw('WEEK(transactionpersonassets.datein, 1) AS datein_week'),
                DB::raw('YEAR(transactionpersonassets.datein) AS datein_year'),
                'personassets.code',
                'personassets.name',
                'personassets.brand',
                'deliveryorders.pickup_address',
                'deliveryorders.pickup_postcode',
                'deliveryorders.pickup_location_name AS from_location_name'
            )
            ->whereNotNull('transactionpersonassets.datein')
            ->whereNull('transactionpersonassets.dateout')
            ->where('transactionpersonassets.is_warehouse', 1);

        // die(var_dump($items->get()));
        // reading whether search input is filled
        if ($datefrom) {
            $items = $items->where(function($query) use ($datefrom){
                 $query->whereDate ('transactionpersonassets.datein', '>=', $datefrom);
            });
        }

        if ($dateto) {
            $items = $items->where(function ($query) use ($dateto) {
                $query->whereDate('transactionpersonassets.datein', '<=', $dateto);
            });
        }

        if($code) {
            $items = $items->where( 'personassets.code', 'LIKE', '%'.$code.'%');
        }

        if($name) {
            $items = $items->where('personassets.name', 'LIKE', '%'.$name.'%');
        }

        if($brand){
            $items = $items->where('personassets.brand', 'LIKE', '%'.$brand.'%');
        }

        if($serial_no){
             $items = $items->where('transactionpersonassets.serial_no', 'LIKE', '%'.$serial_no.'%');
        }

        if($from_location){
            $items = $items->where('deliveryorders.pickup_location_name', 'LIKE', '%'.$from_location.'%');
        }

        if($from_invoice){
            $items = $items->where( 'transactionpersonassets.transaction_id', 'LIKE', '%'.$from_invoice.'%');
        }

        if (request('sortName')) {
            $items = $items->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $items = $items->latest('deliveryorders.pickup_date');
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if ($pageNum == 'All') {
            $items = $items->get();
        } else {
            $items = $items->paginate($pageNum);
        }
        // dd($items->toArray());
        $data = [
            'data' => $items
        ];

        return $data;
    }
}
