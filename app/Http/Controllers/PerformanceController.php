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
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $tasks = Task::with('createdBy', 'updatedBy');

        if (request('sortName')) {
            $tasks = $tasks->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if ($pageNum == 'All') {
            $tasks = $tasks->latest('created_at')->get();
        } else {
            $tasks = $tasks->latest('created_at')->paginate($pageNum);
        }

        $data = [
            'model' => $vms
        ];
        return $data;
    }
}
