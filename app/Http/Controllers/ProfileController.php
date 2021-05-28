<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;

use App\Profile;
use App\Person;

class ProfileController extends Controller
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
        $profile =  Profile::all();

        return $profile;
    }

    /**
     * Return viewing page.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('profile.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('profile.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // if(!request()->has('gst') and request()->has('is_gst_inclusive')) {
        //     Flash::error('GST tickbox is required for GST inclusive');
        //     return redirect()->action('ProfileController@edit', $profile->id);
        // }
        $gst = request()->has('gst') ? 1 : 0;
        $is_gst_inclusive = request()->has('is_gst_inclusive') ? 1 : 0;
        request()->merge(array('gst' => $gst));
        request()->merge(array('is_gst_inclusive' => $is_gst_inclusive));

        $input = request()->all();
        $profile = Profile::create($input);
        // if($request->hasFile('logo')){
        // }
        return redirect('profile');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $profile = Profile::findOrFail($id);
        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request, $id)
    {
        $profile = Profile::findOrFail($id);

        if(!request()->has('gst') and request()->has('is_gst_inclusive')) {
            Flash::error('GST tickbox is required for GST inclusive');
            return redirect()->action('ProfileController@edit', $profile->id);
        }

        // validate is gst rate filled
        if(request()->has('gst')) {
            if(request('gst_rate') == null) {
                Flash::error('Please fill in the GST rate');
                return redirect()->action('ProfileController@edit', $profile->id);
            }
        }

        $gst = request()->has('gst') ? 1 : 0;
        $is_gst_inclusive = request()->has('is_gst_inclusive') ? 1 : 0;
        request()->merge(array('gst' => $gst));
        request()->merge(array('is_gst_inclusive' => $is_gst_inclusive));

        if ((request('gst_rate') != $profile->gst_rate) or (request('gst') != $profile->gst) or (request('is_gst_inclusive') != $profile->is_gst_inclusive)) {
            $people = Person::where('profile_id', $profile->id)->get();

            foreach ($people as $person) {
                $person->is_gst_inclusive = request('is_gst_inclusive');
                $person->gst_rate = request('gst_rate');
                $person->save();
            }
        }

        if($profile->is_gst_inclusive != $is_gst_inclusive ){
            $people = $profile->people;

            foreach($people as $person) {
                $person->is_gst_inclusive = $is_gst_inclusive;
                $person->save();
            }
        }

        $input = request()->all();
        $profile->update($input);

        return redirect()->action('ProfileController@edit', $profile->id);
    }

    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $this->removeFile($profile);
        $profile->delete();
    }

    public function destroyApi($id)
    {
        $profile = Profile::findOrFail($id);
        $profile->delete();
    }

    //adding file
    //@param file var, request
    private function addFile($file, $request)
    {
        $file = $request->file($file);
        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
        // $file->move('cust_asset/profile', $name);

        Storage::put('cust_asset/profile/'.$name, file_get_contents($file->getRealPath()), 'public');
        $url = (Storage::url('cust_asset/profile/'.$name));
        // $person->files()->create(['path' => $url]);
    }

    //remove file and delete file path
    //param fileid
    private function removeFile(Profile $profile)
    {
        $path = public_path();
        $logo = $profile->logo;
        $header = $profile->header;
        $footer = $profile->footer;
        File::delete($logo);
        File::delete($header);
        File::delete($footer);
    }
}
