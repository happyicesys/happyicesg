<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

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
        $jobs = Job::with(['updater', 'creator', 'person']);

        // reading whether search input is filled
        if (request('title')) {
            $title = request('title');
            $personmaintenances = $personmaintenances->where('title', 'LIKE', '%' . $title . '%');
        }

        if (request('person_id')) {
            $person_id = request('person_id');
            $personmaintenances = $personmaintenances->where('person_id', $person_id);
        }

        if (request('created_from')) {
            $created_from = request('created_from');
            $personmaintenances = $personmaintenances->whereDate('created_at', '>=', $created_from);
        }

        if (request('created_to')) {
            $created_to = request('created_to');
            $personmaintenances = $personmaintenances->whereDate('created_at', '<=', $created_to);
        }

        if (request('sortName')) {
            $personmaintenances = $personmaintenances->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        } else {
            $personmaintenances = $personmaintenances->latest();
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if ($pageNum == 'All') {
            $personmaintenances = $personmaintenances->get();
        } else {
            $personmaintenances = $personmaintenances->paginate($pageNum);
        }

        $data = [
            'personmaintenances' => $personmaintenances
        ];

        return $data;
    }

    // create person maintenance()
    public function createPersonmaintenanceApi()
    {
        $personmaintenance = Personmaintenance::create([
            'person_id' => request('person_id'),
            'title' => request('title'),
            'remarks' => request('remarks'),
            'created_by' => auth()->user()->id,
            'created_at' => request('created_at'),
            'is_refund' => (request('refund_name') or request('refund_bank')) ? 1 : 0,
            'refund_name' => request('refund_name'),
            'refund_bank' => request('refund_bank'),
            'refund_account' => request('refund_account'),
            'refund_contact' => request('refund_contact')
        ]);
    }

    // update person maintenance()
    public function updatePersonmaintenanceApi()
    {
        // dd(request()->all());
        $personmaintenance = Personmaintenance::findOrFail(request('id'));

        $personmaintenance->update([
            'person_id' => request('person_id'),
            'title' => request('title'),
            'remarks' => request('remarks'),
            'created_at' => request('created_at'),
            'is_refund' => (request('refund_name') or request('refund_bank')) ? 1 : 0,
            'refund_name' => request('refund_name'),
            'refund_bank' => request('refund_bank'),
            'refund_account' => request('refund_account'),
            'refund_contact' => request('refund_contact'),
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
    public function destroyPersonmaintenanceApi($id)
    {
        $personmaintenance = Personmaintenance::findOrFail($id);
        $personmaintenance->delete();
    }
}
