<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Person;
use Carbon\Carbon;
use App\StoreFile;
use App\Price;
use App\Transaction;
use App\Freezer;
use App\Accessory;

class PersonController extends Controller
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
        $person =  Person::all();

        return $person;
    }  

    /**
     * Return viewing page.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('person.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('person.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PersonRequest $request)
    {
        $input = $request->all();

        $person = Person::create($input);

        return redirect('person');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $person = Person::findOrFail($id);

        $files = StoreFile::wherePersonId($id)->latest()->paginate(5);

        $prices = Price::wherePersonId($id)->oldest()->paginate(50);

        return view('person.edit', compact('person', 'files', 'prices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $person = Person::findOrFail($id);

        $files = StoreFile::wherePersonId($id)->oldest()->paginate(5);

        $prices = Price::wherePersonId($id)->oldest()->paginate(50);

        return view('person.edit', compact('person', 'files', 'prices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonRequest $request, $id)
    {

        $person = Person::findOrFail($id);

        if($request->input('active')){

            if($person->active == 'Yes'){

                $request->merge(array('active' => 'No'));

            }else{

                $request->merge(array('active' => 'Yes'));
            }
            
        }        

        $input = $request->all();

        $person->update($input);

        $this->syncFreezer($person, $request);        

        $this->syncAccessory($person, $request);

        return Redirect::action('PersonController@edit', $person->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = Person::findOrFail($id);

        $person->delete();

        return redirect('person');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return json
     */
    public function destroyAjax($id)
    {
        $person = Person::findOrFail($id);

        $person->delete();

        return $person->name . 'has been successfully deleted';
    }

    public function addFile(Request $request, $id)
    {
        $person = Person::findOrFail($id);

        $file = $request->file('file');

        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();

        $file->move('person_asset/file', $name);

        $person->files()->create(['path' => "/person_asset/file/{$name}"]);

    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function removeFile($id)
    {
        $file = StoreFile::findOrFail($id);

        $filename = $file->path;

        $path = public_path();

        if (!File::delete($path.$filename))
        {
            return Redirect::action('PersonController@edit', $file->person_id);
        }
        else
        {
            $file->delete();
            return Redirect::action('PersonController@edit', $file->person_id);
        }
    }

    public function showTransac($person_id)
    {
        return Transaction::with('user')->wherePersonId($person_id)->get();
    } 

    private function syncFreezer($person, $request)
    {
        if ( ! $request->has('freezer_list'))
        {
            $person->freezers()->detach();
            return;
        }

        $allFreezersId = array();

        foreach ($request->freezer_list as $freezerId)
        {
            if (substr($freezerId, 0, 4) == 'new:')
            {
                $newFreezer = Freezer::create(['name'=>substr($freezerId, 4)]);
                $allFreezersId[] = $newFreezer->id;
                continue;
            }
            $allFreezersId[] = $freezerId;
        }

        $person->freezers()->sync($allFreezersId);
    } 

    private function syncAccessory($person, $request)
    {
        if ( ! $request->has('accessory_list'))
        {
            $person->accessories()->detach();
            return;
        }

        $allAccessoriesId = array();

        foreach ($request->accessory_list as $accessoryId)
        {
            if (substr($accessoryId, 0, 4) == 'new:')
            {
                $newAccessory = Accessory::create(['name'=>substr($accessoryId, 4)]);
                $allAccessoriesId[] = $newAccessory->id;
                continue;
            }
            $allAccessoriesId[] = $accessoryId;
        }

        $person->accessories()->sync($allAccessoriesId);
    }                        
  
}
