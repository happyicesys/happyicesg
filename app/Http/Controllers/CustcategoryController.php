<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CustcategoryRequest;
use App\Http\Requests;
use App\Custcategory;
use App\User;

class CustcategoryController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // retrieve customer categories index api()
    public function getData()
    {
    	$custcats = Custcategory::orderBy('name')->get();
    	return $custcats;
    }

    // return create new cust category page()
    public function create()
    {
        return view('user.custcat.create');
    }

    // store new created cust category(FormRequest $request)
    public function store(CustcategoryRequest $request)
    {
        $input = $request->all();
        $custcat = Custcategory::create($input);
        return redirect()->action(
            'CustcategoryController@edit', ['id' => $custcat->id]
        );
    }

    // retrieve single custcategory api(int id)
    public function getCustcategoryApi($id)
    {
        $custcat = Custcategory::findOrFail($id);

        return $custcat;
    }

    // return cust category edit page(int custcategory_id)
    public function edit($id)
    {
    	$custcat = Custcategory::findOrFail($id);
    	return view('user.custcat.edit', compact('custcat'));
    }

    // update cust category(FormRequest $request, int $custcategory_id)
    public function update(CustcategoryRequest $request, $id)
    {
        $custcat = Custcategory::findOrFail($id);
        $input = $request->all();
        $custcat->update($input);
        return redirect()->action(
            'CustcategoryController@edit', ['id' => $custcat->id]
        );
    }

    // ajax destroy cust category (int $custcategory_id)
    public function destroyAjax($id)
    {
        $custcat = Custcategory::findOrFail($id);
        $custcat->delete();
        return $custcat->name . 'has been successfully deleted';
        return redirect('user');
    }

    // ajax destroy cust category (int $custcategory_id)
    public function destroy($id)
    {
        $custcat = Custcategory::findOrFail($id);
        $custcat->delete();
        return redirect('user');
    }

    // return custcategories by user id given
    public function getCustcategoryByUserIdApi($userId, $type = 1)
    {
        if($type == 1) {
            $user = User::findOrFail($userId);

            return $user->custcategories;

        }else if($type == 2) {
            $custcategories = Custcategory::whereDoesntHave('users', function($query) use ($userId) {
                            $query->where('id', $userId);
                        })->get();

            return $custcategories;
        }
    }
}
