<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Postcode;
use Carbon\Carbon;

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

    // export postcodes into excel files
    public function exportPostcode()
    {
        $title = 'Postcodes';
        $postcodes = Postcode::orderBy('value', 'asc')->get();
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($postcodes) {
            $excel->sheet('sheet1', function($sheet) use ($postcodes) {
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array(
                    'A:T' => '@'
                ));
                $sheet->loadView('excel.postcode', compact('postcodes'));
            });
        })->download('xls');
    }
}
