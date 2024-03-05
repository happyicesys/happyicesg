<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

use App\Http\Requests;

class PerformanceController extends Controller
{
    public function officeIndex()
    {
        return view('performance.office.index');
    }

    public function getOfficeIndexApi()
    {
        $tasks = Task::with('createdBy', 'updatedBy')->get();

        return $tasks;
    }
}
