<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Role;
use App\Permission;

class RoleController extends Controller
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
        $role =  Role::with('permissions')->get();

        return $role;
    } 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $label = $request->input('label');

        $request->merge(array('name' => $this->formatLabel($label)));

        $input = $request->all();

        $role = Role::create($input);

        $this->syncPermission($role, $request);

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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        return view('user.role.edit', compact('role'));        
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
        $role = Role::findOrFail($id);

        $input = $request->all();

        $this->syncPermission($role, $request);

        $role->update($input);

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
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect('user');
    }

    private function formatLabel($label)
    {
        $label = strtolower($label);

        $label = str_replace(' ', '_', $label);

        return $label;
    }

    public function syncPermission(Role $role, $request)
    {
        if ( ! $request->has('permission_list'))
        {
            $role->permissions()->detach();
            return;
        }        

        $role->permissions()->sync($request->input('permission_list'));

    }    
}
