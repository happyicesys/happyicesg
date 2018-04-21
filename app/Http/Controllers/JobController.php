<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Job;

class JobController extends Controller
{

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // return person maintenance index()
    public function getJobIndex()
    {
        return view('job.index');
    }

    // retrieve person maintenance api()
    public function getJobsApi()
    {
        $jobs = Job::with(['updater', 'creator']);

        $jobs = $this->jobFilter($jobs);
        // $jobs = $jobs->get();

        $data = [
            'jobs' => $jobs
        ];

        return $data;
    }

    // create person maintenance()
    public function createJobApi()
    {
        $job = Job::create([
            'task_name' => request('task_name'),
            'progress' => request('progress'),
            'remarks' => request('remarks'),
            'task_date' => request('task_date'),
            'created_by' => auth()->user()->id,
        ]);
    }

    // update person maintenance()
    public function updateJobApi()
    {
        // dd(request()->all());
        $job = Job::findOrFail(request('id'));

        $job->update([
            'task_name' => request('task_name'),
            'progress' => request('progress'),
            'remarks' => request('remarks'),
            'task_date' => request('task_date'),
            'updated_by' => auth()->user()->id
        ]);
    }    

    // get all people api()
    public function getPeopleOptionsApi()
    {
        $people = Person::orderBy('cust_id')->get();
        return $people;
    }

    // remove single personmaintenance api(integer id)
    public function destroyJobApi($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();
    }

    // update verification of job()
    public function verifyJobApi()
    {
        $job = Job::findOrFail(request('job_id'));
        $job->is_verify = request('is_verify');
        $job->save();
    }

    // jobs filter
    private function jobFilter($jobs)
    {
        $task_name = request('task_name');
        $from = request('from');
        $to = request('to');
        $progress = request('progress');
        
        // reading whether search input is filled
        if($task_name) {
            $jobs = $jobs->where('task_name', 'LIKE', '%' . $task_name . '%');
        }

        if($from) {
            $jobs = $jobs->whereDate('task_date', '>=', $from);
        }

        if($to) {
            $jobs = $jobs->whereDate('task_date', '<=', $to);
        }        

        if($progress) {
            switch($progress) {
                case 'In Progress':
                    $jobs = $jobs->where('progress', '<', 100);
                    break;
                case 'Completed':
                    $jobs = $jobs->where('progress', '=', 100);
                    break;                    
            }
        }else {
            $jobs = $jobs->orWhere('progress', '<', 100);
        }

        if (request('sortName')) {
            $jobs = $jobs->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $jobs = $jobs->latest();
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if ($pageNum == 'All') {
            $jobs = $jobs->get();
        } else {
            $jobs = $jobs->paginate($pageNum);
        }

        return $jobs;
    }
}
