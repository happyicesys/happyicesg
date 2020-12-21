<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\MeetingMinute;

class MeetingMinuteController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get data api
    public function getDataApi(Request $request)
    {
        $model = MeetingMinute::with(['creator', 'updater']);

        $model = $this->meetingMinuteFilter($model, $request);

        if ($request->sortName) {
            $model = $model->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $pageNum = $request->pageNum ? $request->pageNum : 100;

        if ($pageNum == 'All') {
            $model = $model->orderBy('created_at', 'desc')->get();
        } else {
            $model = $model->orderBy('created_at', 'desc')->paginate($pageNum);
        }

        return [
            'data' => $model
        ];
    }

    // store new meeting minute(Request $request)
    public function storeUpdateApi(Request $request)
    {
        $id = $request->id;
        $currentUserId = auth()->user()->id;

        if($id) {
            $model = MeetingMinute::findOrFail($id);
            $model->update($request->all());
            $model->updated_by = $currentUserId;
            $model->save();
        }else {
            $model = MeetingMinute::create($request->all());
            $model->created_by = $currentUserId;
            $model->save();
        }
    }

    // meetingMinuteFilter
    private function meetingMinuteFilter($query, $request)
    {
        $date = $request->date;
        $details = $request->details;
        $created_at = $request->created_at;
        $updated_at = $request->updated_at;

        if($date) {
            $query = $query->whereDate('date', '=', $date);
        }

        if($details) {
            $query = $query->where('details', 'LIKE', '%'.$details.'%');
        }

        if($created_at) {
            $query = $query->whereDate('created_at', '=', $created_at);
        }

        if($updated_at) {
            $query = $query->whereDate('updated_at', '=', $updated_at);
        }

        return $query;
    }
}
