<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Profile;


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
    public function store(Request $request)
    {
        $gst = $request->has('gst')? 1 : 0;
        $request->merge(array('gst' => $gst));
        $input = $request->all();
        $profile = Profile::create($input);
        if($request->hasFile('logo')){
        }
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
        $gst = $request->has('gst')? 1 : 0;
        $request->merge(array('gst' => $gst));
        $input = $request->all();
/*        if($request->hasFile('logo')){
            dd('yeah');
        }*/
        $profile->update($input);
        return redirect('profile');
    }

    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $this->removeFile($profile);
        $profile->delete();
    }

    //adding file
    //@param file var, request
    private function addFile($file, $request)
    {
        $file = $request->file($file);
        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
        $file->move('cust_asset/profile', $name);
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
