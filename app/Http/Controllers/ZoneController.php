<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Zone;

class ZoneController extends Controller
{
    public function getAllZoneApi()
    {
        $zones = Zone::all();
        return $zones;
    }

    // retrieve all zones api()
    public function getIndexApi()
    {
    	$zones = Zone::all();
    	return $zones;
    }

    // return create new zone page()
    public function create()
    {
        return view('user.zone.create');
    }

    // store new created zone(FormRequest $request)
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $input = $request->all();
        $data = Zone::create($input);
        return redirect()->action(
            'ZoneController@edit', ['id' => $data->id]
        );
    }

    // retrieve single zone api(int id)
    public function getZoneApi($id)
    {
        $data = Zone::findOrFail($id);
        return $data;
    }

    // return zone edit page(int zoneId)
    public function edit($id)
    {
    	$zone = Zone::findOrFail($id);
    	return view('user.zone.edit', compact('zone'));
    }

    // update zone(FormRequest $request, int $custcategory_id)
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $data = Zone::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        return redirect()->action(
            'ZoneController@edit', ['id' => $data->id]
        );
    }

    // ajax destroy zone (int $zoneId)
    public function destroyAjax($id)
    {
        $data = Zone::findOrFail($id);
        $data->delete();
        return $data->name . 'has been successfully deleted';
        return redirect('user');
    }

    // ajax destroy zone (int $zoneId)
    public function destroy($id)
    {
        $data = Zone::findOrFail($id);
        $data->delete();
        return redirect('user');
    }
}
