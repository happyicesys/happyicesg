<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Truck;

class TruckController extends Controller
{
    // retrieve all trucks api()
    public function getIndexApi()
    {
    	$trucks = Truck::with('driver')->get();
    	return $trucks;
    }

    // return create new truck page()
    public function create()
    {
        return view('user.truck.create');
    }

    // store new created truck(FormRequest $request)
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $input = $request->all();
        $data = Truck::create($input);
        return redirect()->action(
            'TruckController@edit', ['id' => $data->id]
        );
    }

    // retrieve single truck api(int id)
    public function getTruckApi($id)
    {
        $data = Truck::findOrFail($id);
        return $data;
    }

    // return truck edit page(int truckId)
    public function edit($id)
    {
    	$truck = Truck::findOrFail($id);
    	return view('user.truck.edit', compact('truck'));
    }

    // update truck(FormRequest $request, int $custcategory_id)
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $data = Truck::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        return redirect()->action(
            'TruckController@edit', ['id' => $data->id]
        );
    }

    // ajax destroy truck (int $truckId)
    public function destroyAjax($id)
    {
        $data = Truck::findOrFail($id);
        $data->delete();
        return $data->name . 'has been successfully deleted';
        return redirect('user');
    }

    // ajax destroy truck (int $truckId)
    public function destroy($id)
    {
        $data = Truck::findOrFail($id);
        $data->delete();
        return redirect('user');
    }
}
