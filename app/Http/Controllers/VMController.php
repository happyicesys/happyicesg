<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Chrisbjr\ApiGuard\Http\Controllers\ApiGuardController;

class VMController extends ApiGuardController
{
    public function index()
    {
        return 'hello';
    }
}
