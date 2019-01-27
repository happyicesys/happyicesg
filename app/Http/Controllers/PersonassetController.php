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

        // reading whether search input is filled
        if($person_id) {
            $personassets = $personassets->where('person_id', $person_id);
        }

        if($code) {
            $personassets = $personassets->where('code', 'LIKE', '%'.$code.'%');
        }

        if($name) {
            $personassets = $personassets->where('name', 'LIKE', '%' . $name . '%');
        }

        if($brand) {
            $personassets = $personassets->where('brand', 'LIKE', '%' . $brand . '%');
        }

        if (request('sortName')) {
            $personassets = $personassets->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $personassets = $personassets->latest();
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

    // retrieve api for person asset movement()
    public function indexMovementApi()
    {
        // $year = request('year');
        // $week = request('week');
        $datefrom = request('datefrom');
        $dateto = request('dateto');

        $items = DB::table('transactionpersonassets')
            ->leftJoin('transactions', 'transactions.id', '=', 'transactionpersonassets.transaction_id')
            ->leftJoin('deliveryorders', 'deliveryorders.transaction_id', '=', 'transactions.id')
            ->leftJoin('personassets', 'personassets.id', '=', 'transactionpersonassets.personasset_id')
            ->select(
                'transactionpersonassets.id AS id', 'transactionpersonassets.transaction_id', 'transactionpersonassets.personasset_id',
                'transactionpersonassets.serial_no', 'transactionpersonassets.sticker', 'transactionpersonassets.remarks',
                DB::raw('DATE(transactionpersonassets.datein) AS datein'),
                DB::raw('DATE(transactionpersonassets.dateout) AS dateout'),
                DB::raw('WEEK(transactionpersonassets.datein, 1) AS datein_week'),
                DB::raw('YEAR(transactionpersonassets.datein) AS datein_year'),
                DB::raw('WEEK(transactionpersonassets.dateout, 1) AS dateout_week'),
                DB::raw('YEAR(transactionpersonassets.dateout) AS dateout_year'),
                'personassets.code', 'personassets.name', 'personassets.brand',
                'deliveryorders.pickup_address', 'deliveryorders.pickup_postcode'
            )
            ->where('transactions.is_deliveryorder', 1);
            // ->where('transactionpersonassets.is_warehouse', 1);

        // reading whether search input is filled
        if ($datefrom) {
            $items = $items->where(function($query) use ($datefrom){
                $query->whereNull('transactionpersonassets.dateout')
                    ->whereDate('transactionpersonassets.datein', '>=', $datefrom)
                    ->orWhere(DB::raw('DATE(transactionpersonassets.dateout)', '>=', $datefrom));
            });
        }

        if ($dateto) {
            $items = $items->where(function ($query) use ($dateto) {
                $query->whereNull('transactionpersonassets.dateout')
                    ->whereDate('transactionpersonassets.datein', '<=', $dateto)
                    ->orWhere(DB::raw('DATE(transactionpersonassets.dateout)', '<=', $dateto));
            });
        }

        if (request('sortName')) {
            $items = $items->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $items = $items->latest('transactionpersonassets.created_at');
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
