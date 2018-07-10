<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Chrisbjr\ApiGuard\Http\Controllers\ApiGuardController;

use App\Http\Requests;

class VMController extends ApiGuardController
{
    public function index()
    {
        return 'hello';
    }
}
