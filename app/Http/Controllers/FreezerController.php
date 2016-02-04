<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\FreezerRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Freezer;

class FreezerController extends Controller
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
        $freezers =  Freezer::all();

        return $freezers;
    } 

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.freezer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FreezerRequest $request)
    {
        $input = $request->all();

        $freezer = Freezer::create($input);

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
        $freezer = Freezer::findOrFail($id);

        return view('user.freezer.edit', compact('freezer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $freezer = Freezer::findOrFail($id);

        return view('user.freezer.edit', compact('freezer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FreezerRequest $request, $id)
    {
        $freezer = Freezer::findOrFail($id);

        $input = $request->all();

        $freezer->update($input);

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
        $freezer = Freezer::findOrFail($id);

        $freezer->delete();

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
        $freezer = Freezer::findOrFail($id);

        $freezer->delete();

        return $freezer->name . 'has been successfully deleted';
    }
}
