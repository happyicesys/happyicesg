<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// use Chrisbjr\ApiGuard\Http\Controllers\ApiGuardController;
use DB;
use App\CashlessTerminal;
use App\Vending;
use App\Simcard;
use Auth;

class VMController extends Controller
{

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['dataIndex']]);
    }

    // get vending index page
    public function vendingIndex()
    {
        return view('vm.index');
    }

    // get vending index api
    public function getVendingIndexApi()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $vms = $this->getVmsData();

        if (request('sortName')) {
            $vms = $vms->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if ($pageNum == 'All') {
            $vms = $vms->latest('vendings.created_at')->get();
        } else {
            $vms = $vms->latest('vendings.created_at')->paginate($pageNum);
        }

        $data = [
            'vms' => $vms
        ];
        return $data;
    }

    // return vending create page
    public function getVendingCreate()
    {
        return view('vm.create');
    }

    public function storeVending(Request $request)
    {
        $this->validate($request, [
            'serial_no' => 'required|unique:vendings,serial_no',
        ], [
            'serial_no.required' => 'Please fill in serial number',
            'serial_no.unique' => 'The serial number has been used'
        ]);

        $input = $request->all();

        Vending::create($input);

        return redirect('vm');
    }

    public function destroyVending($id)
    {
        $vending = Vending::findOrFail($id);

        $vending->delete();
    }

    public function editVending($id)
    {
        $vending = Vending::findOrFail($id);

        return view('vm.edit', compact('vending'));
    }

    public function updateVending(Request $request, $id)
    {
        $input = $request->all();

        // dd($input);
        $vending = Vending::findOrFail($id);

        $request->merge(array('updated_by' => Auth::user()->name));

        $vending->update($input);

        return redirect('vm');
    }

    // get cashless index page
    public function cashlessIndex()
    {
        return view('cashless.index');
    }

    // get cashless index api
    public function getCashlessIndexApi()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $cashlessTerminals = $this->getCashlessTerminalsData();

        if (request('sortName')) {
            $cashlessTerminals = $cashlessTerminals->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if ($pageNum == 'All') {
            $cashlessTerminals = $cashlessTerminals->latest('cashless_terminals.created_at')->get();
        } else {
            $cashlessTerminals = $cashlessTerminals->latest('cashless_terminals.created_at')->paginate($pageNum);
        }

        $data = [
            'cashlessTerminals' => $cashlessTerminals
        ];
        return $data;
    }

    // return cashless create page
    public function getCashlessCreate()
    {
        return view('cashless.create');
    }

    // store new cashless data(Request request)
    public function createCashlessApi(Request $request)
    {
        $input = $request->all();
        CashlessTerminal::create($input);
    }

    // remove cashless entry (integer id)
    public function destroyCashlessApi($id)
    {
        $cashless = CashlessTerminal::findOrFail($id);

        $cashless->delete();
    }

    // return cashless edit page(integer id)
    public function editCashless($id)
    {
        $cashless = CashlessTerminal::findOrFail($id);

        return view('cashless.edit', compact('cashless'));
    }

    // update cashless entry(Request request, integer id)
    public function updateCashlessApi(Request $request, $id)
    {
        $input = $request->all();

        $cashless = CashlessTerminal::findOrFail($id);

        $cashless->update($input);

        return redirect('cashless');
    }

    // get cashless index page
    public function simcardIndex()
    {
        return view('simcard.index');
    }

    // get cashless index api
    public function getSimcardIndexApi()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $simcards = $this->getSimcardsData();

        if (request('sortName')) {
            $simcards = $simcards->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if ($pageNum == 'All') {
            $simcards = $simcards->latest('simcards.created_at')->get();
        } else {
            $simcards = $simcards->latest('simcards.created_at')->paginate($pageNum);
        }

        $data = [
            'simcards' => $simcards
        ];
        return $data;
    }

    // return simcard create page
    public function getSimcardCreate()
    {
        return view('simcard.create');
    }

    // store new simcard data(Request request)
    public function createSimcardApi(Request $request)
    {
        // $telcoName = $request->telco_name;
        // if($telcoName) {
        //     if($telcoName === 'Singtel_IMSI') {
        //         $this->validate($request, [
        //             'simcard_no' => 'digits:15'
        //         ]);
        //     }else if($telcoName === 'Starhub_ICCID') {
        //         $this->validate($request, [
        //             'simcard_no' => 'digits:18'
        //         ]);
        //     }
        // }
        $request->merge(array('updated_by' => Auth::user()->name));
        $input = $request->all();
        Simcard::create($input);
    }

    // remove simcard entry (integer id)
    public function destroySimcardApi($id)
    {
        $simcard = Simcard::findOrFail($id);

        $simcard->delete();
    }

    // return simcard edit page(integer id)
    public function editSimcard($id)
    {
        $simcard = Simcard::findOrFail($id);

        return view('simcard.edit', compact('simcard'));
    }

    // update simcard entry(Request request, integer id)
    public function updateSimcardApi(Request $request, $id)
    {
        $telcoName = $request->telco_name;
        if($telcoName) {
            if($telcoName === 'Singtel_IMSI') {
                $this->validate($request, [
                    'simcard_no' => 'digits:15'
                ], [
                  'simcard_no.digits' => 'Please enter 15 digits for Singtel',
                ]);
            }else if($telcoName === 'Starhub_ICCID') {
                $this->validate($request, [
                    'simcard_no' => 'digits:18'
                ], [
                    'simcard_no.digits' => 'Please enter 18 digits for Starhub',
                ]);
            }
        }

        $this->validate($request, [
            'telco_name' => 'required'
        ]);

        $input = $request->all();

        $simcard = Simcard::findOrFail($id);

        $simcard->update($input);

        return back();
    }

    // store simcard entry(Request request, integer id)
    public function storeSimcard(Request $request)
    {
        $telcoName = $request->telco_name;
        if($telcoName) {
            if($telcoName === 'Singtel_IMSI') {
                $this->validate($request, [
                    'simcard_no' => 'required|digits:15'
                ], [
                  'simcard_no.digits' => 'Please enter 15 digits for Singtel',
                ]);
            }else if($telcoName === 'Starhub_ICCID') {
                $this->validate($request, [
                    'simcard_no' => 'required|digits:18'
                ], [
                    'simcard_no.digits' => 'Please enter 18 digits for Starhub',
                ]);
            }
        }

        $this->validate($request, [
            'simcard_no' => 'required',
            'telco_name' => 'required'
        ]);

        $input = $request->all();

        $simcard = Simcard::create($input);

        return view('simcard.index');
    }

    // retrieve vms data ()
    private function getVmsData()
    {
        $vendings = DB::table('vendings')
            ->leftJoin('people', 'vendings.person_id', '=', 'people.id')
            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
            ->leftJoin('custcategories', 'people.custcategory_id', '=', 'custcategories.id')
            ->leftJoin('simcards', 'simcards.id', '=', 'vendings.simcard_id')
            ->leftJoin('cashless_terminals', 'cashless_terminals.id', '=', 'vendings.cashless_terminal_id')
            ->select(
                'people.cust_id', 'people.company', 'people.id as person_id',
                'vendings.vend_id', 'vendings.serial_no', 'vendings.type', 'vendings.router', 'vendings.desc', 'vendings.updated_by', 'vendings.created_at', 'vendings.id', 'vendings.updated_at', 'vendings.id AS id',
                'profiles.id as profile_id',
                'custcategories.name as custcategory',
                'simcards.phone_no', 'simcards.telco_name', 'simcards.simcard_no', 'simcards.id AS simcard_id',
                'cashless_terminals.provider_name', 'cashless_terminals.terminal_id'
            );

        // reading whether search input is filled
        $vendings = $this->searchDBFilter($vendings);

        return $vendings;
    }

    // pass value into filter search for DB (collection, collection request) [query]
    private function searchDBFilter($vendings)
    {
        if (request('vend_id')) {
            $vendings = $vendings->where('vendings.vend_id', 'LIKE', '%' . request('vend_id') . '%');
        }
        if (request('type')) {
            $vendings = $vendings->where('vendings.type', 'LIKE', '%' . request('type') . '%');
        }
        if (request('desc')) {
            $vendings = $vendings->where('vendings.desc', 'LIKE', '%' . request('desc') . '%');
        }
        if (request('cust_id')) {
            $vendings = $vendings->where('people.cust_id', 'LIKE', '%' . request('cust_id') . '%');
        }
        if (request('serial_no')) {
            $vendings = $vendings->where('vendings.serial_no', 'LIKE', '%' . request('serial_no') . '%');
        }
        if (request('company')) {
            $com = request('company');
            $vendings = $vendings->where(function ($query) use ($com) {
                $query->where('people.company', 'LIKE', '%' . $com . '%')
                    ->orWhere(function ($query) use ($com) {
                        $query->where('people.cust_id', 'LIKE', 'D%')
                            ->where('people.name', 'LIKE', '%' . $com . '%');
                    });
            });
        }
        if (request('custcategory')) {
            $vendings = $vendings->where('custcategories.id', request('custcategory'));
        }

        if (request('sortName')) {
            $vendings = $vendings->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $vendings;
    }

    // retrieve simcards data ()
    private function getSimcardsData()
    {
        $simcards = DB::table('simcards')
            ->leftJoin('vendings', 'vendings.id', '=', 'simcards.vending_id')
            ->leftJoin('users', 'users.id', '=', 'simcards.updated_by')
            ->select(
                'simcards.phone_no', 'simcards.telco_name', 'simcards.simcard_no', 'simcards.id',
                'simcards.updated_by', 'simcards.updated_at',
                'vendings.id AS vending_id', 'vendings.serial_no'
            );

        // reading whether search input is filled
        $simcards = $this->searchSimcardDBFilter($simcards);

        return $simcards;
    }

    // pass value into filter search for DB (collection, collection request) [query]
    private function searchSimcardDBFilter($simcards)
    {
        if (request('phone_no')) {
            $simcards = $simcards->where('simcards.phone_no', 'LIKE', '%' . request('phone_no') . '%');
        }
        if (request('telco_name')) {
            $simcards = $simcards->where('simcards.telco_name', 'LIKE', '%' . request('telco_name') . '%');
        }
        if (request('simcard_no')) {
            $simcards = $simcards->where('simcards.simcard_no', 'LIKE', '%' . request('simcard_no') . '%');
        }
        if (request('sortName')) {
            $simcards = $simcards->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $simcards;
    }


    // retrieve cashless data ()
    private function getCashlessTerminalsData()
    {
        $cashlessTerminals = CashlessTerminal::query()
            ->leftJoin('vendings', 'vendings.id', '=', 'cashless_terminals.vending_id')
            ->select('*', 'cashless_terminals.id AS id');

        $cashlessTerminals = $this->searchCashlessTerminalDBFilter($cashlessTerminals);

        return $cashlessTerminals;
    }

    // pass value into filter search for DB (collection, collection request) [query]
    private function searchCashlessTerminalDBFilter($cashlessTerminals)
    {
        if (request('provider_id')) {
            $cashlessTerminals = $cashlessTerminals->where('cashless_terminals.provider_id', '=', request('provider_id'));
        }
        if (request('sortName')) {
            $cashlessTerminals = $cashlessTerminals->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $cashlessTerminals;
    }

}
