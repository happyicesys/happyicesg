<?php

namespace App\Http\Controllers;

use App\Task;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;

class PerformanceController extends Controller
{
    public function officeIndex()
    {
        return view('performance.office.index');
    }

    public function getOfficeIndexApi(Request $request)
    {
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $tasks = Task::with([
            'createdBy',
            'updatedBy'
        ]);

        if($request->name) {
            $tasks = $tasks->where('name', 'like', '%'.$request->name.'%');
        }

        if($request->date) {
            $tasks = $tasks->where(function($query) use ($request) {
                $query->where('date_from', '>=', Carbon::parse($request->date)->startOfDay())
                    ->orWhere('date_to', '<=', Carbon::parse($request->date)->endOfDay());
            });
        }

        // if($request->status) {
        //     $tasks = $tasks->where('status', $request->status);
        // }

        if (request('sortName')) {
            $tasks = $tasks->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if ($pageNum == 'All') {
            $tasks = $tasks->oldest('created_at')->get();
        } else {
            $tasks = $tasks->oldest('created_at')->paginate($pageNum);
        }

        $dataArr = [];
        $headers = [];
        $contents = [];
        $startDate = Carbon::parse($request->date)->subDays(2)->startOfDay();
        $endDate = Carbon::parse($request->date)->addDays(2)->endOfDay();
        for($date=$startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $headers[] = [
                'date' => $date->format('Y-m-d'),
                'shortDate' => $date->format('ymd'),
                'day' => $date->format('D'),
                'color' => $date->eq(Carbon::parse($request->date)) ? '#0096C7' : '#DDFDF8'
            ];
        }

        foreach($headers as $header) {
            foreach($tasks as $task) {
                if(
                    $task->date_from->lte(Carbon::parse($header['date'])) &&
                    (!$task->date_to || $task->date_to->gte(Carbon::parse($header['date'])))
                ) {
                    // dd($task->toArray()['date_from'], $header['date']);
                    $contents[$header['date']][] = [
                        'name' => $task->name,
                        'date_from' => $task->date_from,
                        'date_to' => $task->date_to,
                        'days_diff' => $task->date_from->diffInDays(Carbon::today()),
                        'desc' => $task->desc,
                        'status' => $task->status,
                        'created_by' => $task->createdBy->name,
                        'updated_by' => $task->updatedBy->name,
                    ];
                }else {
                    $contents[$header['date']][] = null;
                }
            }
        }

        $data = [
            'headers' => $headers,
            'contents' => $contents,
        ];
        return $data;
    }

    public function createTask()
    {
        return view('performance.office.create');
    }

    public function storeTask(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            // 'desc' => 'required',
        ]);

        $task = new Task;
        $task->name = request('name');
        $task->date_from = request('date_from') ? request('date_from') : Carbon::today();
        $task->date_to = request('date_to') ? request('date_to') : null;
        $task->desc = request('desc') ? request('desc') : null;
        $task->status = Task::STATUS_PENDING;
        $task->status_json = [
            'status' => Task::STATUS_PENDING,
            'datetime' => Carbon::now()->toDateTimeString(),
        ];
        $task->created_by = auth()->user()->id;
        $task->updated_by = auth()->user()->id;
        $task->save();

        return Redirect::action('PerformanceController@officeIndex');
    }
}
