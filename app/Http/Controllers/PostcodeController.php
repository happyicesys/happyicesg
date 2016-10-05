<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Postcode;

class PostcodeController extends Controller
{
    // retrieve all postcodes(null)
    public function allPostcodesApi()
    {
        $postcodes = Postcode::all();
        return $postcodes;
    }

    // verify postcode is within coverage (null)
    public function verifyPostcode(Request $request)
    {
        $this->validate($request, [
            'postcode' => 'required|digits:6'
        ]);
        $postcode = Postcode::whereValue($request->postcode)->whereNotNull('person_id')->first();
        if($postcode){
            return $postcode;
        }else{
            return '';
        }
    }
}
