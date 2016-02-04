<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\AccessoryRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Accessory;

class AccessoryController extends Controller
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
        $accessories =  Accessory::all();

        return $accessories;
    } 

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.accessory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccessoryRequest $request)
    {
        $input = $request->all();

        $accessory = Accessory::create($input);

        return redirect('user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $accessory = Accessory::findOrFail($id);

        return view('user.accessory.edit', compact('accessory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $accessory = Accessory::findOrFail($id);

        return view('user.accessory.edit', compact('accessory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccessoryRequest $request, $id)
    {
        $accessory = Accessory::findOrFail($id);

        $input = $request->all();

        $accessory->update($input);

        return redirect('user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $accessory = Accessory::findOrFail($id);

        $accessory->delete();

        return redirect('user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return json
     */
    public function destroyAjax($id)
    {
        $accessory = Accessory::findOrFail($id);

        $accessory->delete();

        return $accessory->name . 'has been successfully deleted';
    }
}
