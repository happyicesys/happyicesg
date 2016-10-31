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
        $covered = false;
        $this->validate($request, [
            'postcode' => 'required|digits:6'
        ]);
        $postcode = Postcode::whereValue($request->postcode)->first();
        if($postcode) {
            $covered = $postcode->person_id ? true : false;
        }
        $data = [
            'postcode' => $postcode,
            'covered' => $covered,
        ];
        return $data;
    }
}
