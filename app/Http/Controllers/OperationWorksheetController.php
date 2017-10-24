<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class OperationWorksheetController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return vending machine page()
    public function getOperationWorksheetIndex()
    {
        return view('detailrpt.operation.index');
    }
}
