<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;

class DetailRptController extends Controller
{
    // detect authed
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return index page for detailed report - account
    public function accountIndex()
    {
        return view('detailrpt.account.index');
    }

    // return index page for detailed report - sales
    public function salesIndex()
    {
        return view('detailrpt.sales.index');
    }
}
