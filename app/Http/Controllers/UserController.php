<?php

namespace App\Http\Controllers;

// use Illuminate\Http\UserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\Profile;
use Auth;

class UserController extends Controller
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
        $user =  User::all();

        return $user;
    }      

    /**
     * Return viewing page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $roles = Role::paginate(10);

        $users = User::paginate(10);

        $profile = Profile::first();

        return view('user.index', compact('roles', 'users', 'profile'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->validate($request, [
            'password' => 'required|confirmed', 
            'password_confirmation' => 'required'
        ]);

        $input = $request->all();

        $user = User::create($input);

        $this->syncRole($user, $request->input('role_list'));

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
        $user = User::findOrFail($id);

        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {

        $user = User::findOrFail($id);

        if($request->has('password')){

            $input = $request->all();

        }else{

            $input = $request->except('password');

        }

        $user->update($input);

        $this->syncRole($user, $request->input('role_list'));

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
        $user = User::findOrFail($id);

        $user->delete();

        return redirect('user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAjax($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return $user->name . 'has been successfully deleted';
    }

    private function syncRole(User $user, $selected_role)
    {
        $user->roles()->sync($selected_role);
    }     

}
