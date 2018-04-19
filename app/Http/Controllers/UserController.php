<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\Profile;
use Auth;
use App\Person;
use Laracasts\Flash\Flash;

class UserController extends Controller
{

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getData()
    {
        $user =  User::with('roles')->whereIn('type', ['admin', 'staff'])->get();
        // $user = User::all();

        return $user;
    }

    // get user id only api
    public function getUser($user_id)
    {
        $user =  User::where('id', $user_id)->with('roles')->firstOrFail();

        return $user;
    }

    public function index()
    {
        $roles = Role::paginate(10);
        $users = User::paginate(10);
        $profile = Profile::first();
        return view('user.index', compact('roles', 'users', 'profile'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(UserRequest $request)
    {
        $this->validate($request, [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
        $request->merge(array('can_access_inv' => request()->has('can_access_inv') ? 1 : 0));
        $input = $request->all();
        $user = User::create($input);
        $this->syncRole($user, $request->input('role_list'));
        $this->assignType($user);
        return redirect('user');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    public function update(UserRequest $request, $id)
    {
        // dd($request->all());
        $user = User::findOrFail($id);
        $request->merge(array('can_access_inv' => request()->has('can_access_inv') ? 1 : 0));

        if($request->has('password')){
            $input = $request->all();
        }else{
            $input = $request->except('password');
        }
        $user->update($input);
        $person = Person::where('user_id', $user->id)->first();

        if($person){
            $person->company = $user->username;
            $person->name = $user->name;
            $person->email = $user->email;
            $person->contact = $user->contact;
            $person->save();
        }

        $this->syncRole($user, $request->input('role_list'));
        $this->assignType($user);
        return redirect()->action('UserController@edit', ['id' => $user->id]);        
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('user');
    }

    public function destroyAjax($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return $user->name . 'has been successfully deleted';
    }

    public function convertInitD($user_id, $level)
    {
        $find_exist = Person::where('user_id', $user_id)->first();

        if(! $find_exist){
            $user = User::findOrFail($user_id);
            $people = Person::withTrashed()->where('cust_id', 'LIKE', 'D%');
            $first_person = Person::where('cust_id', 'D100001')->first();

            if(count($people) > 0 and $first_person){
                $latest_cust = (int) substr($people->max('cust_id'), 1) + 1;
                $latest_cust = 'D'.$latest_cust;
            }else{
                $latest_cust = 'D100001';
            }

            $person = new Person();
            $person->cust_id = $latest_cust;
            $person->cust_type = strtoupper($level);
            $person->user_id = $user->id;
            $person->profile_id = 1;
            $person->name = $user->name;
            $person->company = $user->name;
            $person->contact = $user->contact;
            $person->email = $user->email;
            $person->save();

            if($latest_cust == 'D100001'){
                $person->makeRoot();
            }else{
                $creator = Person::where('user_id', Auth::user()->id)->first();
                if($creator){
                    $person->makeChildOf($creator);
                    $person->parent_name = $creator->name;
                }else{
                    $person->makeRoot();
                }
                $person->save();
            }

            Flash::success('Added Successfully');
        }else{
            Flash::error('The user was already DTD member');
        }
        return redirect('user');
    }

    // retrieve profiles api by given user id(int user_id)
    public function getProfileByUser($user_id)
    {
        $user = User::findOrFail($user_id);

        return $user->profiles;
    }

    // retrieve profiles api where not bind to the user(int user_id)
    public function getNotProfileByUser($user_id)
    {
        $profiles = Profile::whereDoesntHave('users', function($query) use ($user_id) {
                        $query->where('id', $user_id);
                    })->get();

        return $profiles;
    }

    // add profile to the user ($user_id)
    public function addProfileByUser($user_id)
    {
        $user = User::findOrFail($user_id);

        $profile = Profile::findOrFail(request('profile_id'));

        $user->profiles()->attach($profile);
    }

    // remove profile by given user id(int $user_id, int $profile_id)
    public function removeProfileByUser($user_id, $profile_id)
    {
        $user = User::findOrFail($user_id);
        $profile = Profile::findOrFail($profile_id);
        $user->profiles()->detach($profile);
    }

    // control user activation(int $user_id)
    public function userActivationControl($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->is_active = !$user->is_active;
        $user->save();
        return redirect('user');
    }

    private function syncRole(User $user, $selected_role)
    {
        $user->roles()->sync($selected_role);
    }

    private function assignType($user)
    {
        if($user->hasRole('admin')){
            $user->type = 'admin';
        }else{
            $user->type = 'staff';
        }
        $user->save();
    }

}
