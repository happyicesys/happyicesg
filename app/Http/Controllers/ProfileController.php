<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Profile;


class ProfileController extends Controller
{
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
        return view('user.profile.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $profile = Profile::create($input);

        if($request->hasFile('logo')){

        }

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
        //
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

        return view('user.profile.edit', compact('profile'));
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
        $profile = Profile::findOrFail($id);

        $input = $request->all();

        if($request->hasFile('logo')){
            dd('yeah');
        }

        $profile->update($input);

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
