<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\InvRecord;
use Auth;
use App\Item;
use Laracasts\Flash\Flash;
use Session;

class InventoryController extends Controller
{

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getData()
    {
        $inventories =  Inventory::latest()->get();

        return $inventories;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // creation record
        $request->merge(array('creator_id' => Auth::user()->id));

        $request->merge(array('created_by' => Auth::user()->name));

        // retrive stock in array
        $currentQty = $request->current;

        $incomingQty = $request->incoming;

        $afterQty = $request->after;

        // validation for incoming must fill in batch number
        if($request->type == 'Incoming'){

            $this->validate($request, [

                'batch_num' => 'required',

            ]);
        }

        $input = $request->all();

        $inventory = Inventory::create($input);

        // store all the history total into inventory
        $inventory->qtytotal_current = array_sum($currentQty);

        $inventory->qtytotal_incoming = array_sum($incomingQty);

        $inventory->qtytotal_after = array_sum($afterQty);

        $inventory->save();

        if(array_filter($incomingQty)){

            $this->createInvRecord($inventory->id, $currentQty, $incomingQty, $afterQty);

            Flash::success('Entries added');

            return redirect('item');

        }else{

            // Flash::error('Please fill up the form');
            Session::flash('global', 'Please Fill Up The Form');

            return view('inventory.create');

        }

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
        $inventory = Inventory::findOrFail($id);

        $invrecs = InvRecord::where('inventory_id', $inventory->id)->with('item')->get();

        return view('inventory.edit', compact('inventory', 'invrecs'));
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
        $currentArr = $request->current;

        $incomingArr = $request->incoming;

        $afterArr = $request->after;

        $inventory = Inventory::findOrFail($id);

        if(($inventory->qtytotal_current != $request->total_current) or ($inventory->qtytotal_incoming != $request->total_incoming) or ($inventory->qtytotal_after != $request->total_after)){

            foreach($incomingArr as $index => $incoming){

                $invrec = InvRecord::findOrFail($index);

                $invrec->qtyrec_current = $currentArr[$index];

                $invrec->qtyrec_incoming = $incoming;

                $invrec->qtyrec_after = $afterArr[$index];
            }

        }else{


        }



        dd($input);
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

    private function createInvRecord($inv_id, $currentQty, $incomingQty, $afterQty)
    {
        foreach($incomingQty as $index => $qty){

            if($qty != NULL or $qty != 0 ){

                $rec = new InvRecord();

                $rec->inventory_id = $inv_id;

                $rec->item_id = $index;

                $rec->qtyrec_current = $currentQty[$index];

                $rec->qtyrec_incoming = $qty;

                $rec->qtyrec_after = $afterQty[$index];

                $rec->save();

                $item = Item::findOrFail($index);

                $item->qty_last = $currentQty[$index];

                $item->qty_now = $currentQty[$index] + $incomingQty[$index];

                $item->save();
            }
        }
    }
}
