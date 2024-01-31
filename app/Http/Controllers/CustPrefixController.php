<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CustPrefix;

use App\Http\Requests;

class CustPrefixController extends Controller
{
    // retrieve all custPrefixes api()
    public function getIndexApi()
    {
    	$custPrefixes = CustPrefix::query()
            ->withCount('people')
            ->orderBy('code')
            ->get();
    	return $custPrefixes;
    }

    // return create new custPrefix page()
    public function create()
    {
        return view('user.cust_prefix.create');
    }

    // store new created custPrefix(FormRequest $request)
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);

        $input = $request->all();
        $data = CustPrefix::create($input);
        return redirect()->action(
            'CustPrefixController@edit', ['id' => $data->id]
        );
    }

    // retrieve single CustPrefix api(int id)
    public function getCustPrefixApi($id)
    {
        $data = CustPrefix::findOrFail($id);
        return $data;
    }

    // return CustPrefix edit page(int truckId)
    public function edit($id)
    {
    	$custPrefix = CustPrefix::findOrFail($id);
    	return view('user.cust_prefix.edit', compact('custPrefix'));
    }

    // update CustPrefix(FormRequest $request, int $custcategory_id)
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);

        $data = CustPrefix::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        return redirect()->action(
            'CustPrefixController@edit', ['id' => $data->id]
        );
    }

    // ajax destroy CustPrefix (int $truckId)
    public function destroyAjax($id)
    {
        $custPrefix = CustPrefix::findOrFail($id);
        if($custPrefix->people){
            foreach($custPrefix->people as $person){
                $person->cust_prefix_id = null;
                $person->save();
            }
        }
        $custPrefix->delete();
        return $custPrefix->name . 'has been successfully deleted';
        return redirect('user');
    }

    // ajax destroy CustPrefix (int $truckId)
    public function destroy($id)
    {
        $custPrefix = CustPrefix::findOrFail($id);
        if($custPrefix->people){
            foreach($custPrefix->people as $person){
                $person->cust_prefix_id = null;
                $person->save();
            }
        }
        $custPrefix->delete();
        return redirect('user');
    }
}
