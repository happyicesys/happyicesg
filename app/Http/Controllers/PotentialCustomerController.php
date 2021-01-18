<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\PotentialCustomer;
use App\PotentialCustomerAttachment;
use App\SalesProgress;
use App\HasMonthOptions;
use Carbon\Carbon;
use DB;
use File;

class PotentialCustomerController extends Controller
{
    use HasMonthOptions;

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get index page
    public function index()
    {
        $monthOptions = $this->getMonthOptions();

        return view('potential-customer.index', compact('monthOptions'));
    }

    // get data api
    public function getDataApi(Request $request)
    {
        $model = PotentialCustomer::with(['accountManager', 'custcategory', 'creator', 'updater', 'potentialCustomerAttachments', 'salesProgresses']);
                // ->leftJoin('users as account_manager', 'potential_customers.account_manager_id', '=', 'account_manager.id')
                // ->leftJoin('custcategories', 'potential_customers.custcategory_id', '=', 'custcategories.id');

        $model = $this->potentialCustomerFilter($model, $request);

        if ($request->sortName) {
            $model = $model->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $pageNum = $request->pageNum ? $request->pageNum : 100;

        if ($pageNum == 'All') {
            $model = $model->orderBy('potential_customers.created_at', 'desc')->get();
        } else {
            $model = $model->orderBy('potential_customers.created_at', 'desc')->paginate($pageNum);
        }

        return [
            'data' => $model
        ];
    }

    // store new potential customer(Request $request)
    public function storeUpdateApi(Request $request)
    {
        $id = $request->id;
        $currentUserId = auth()->user()->id;
// dd($request->all());
        if($id) {
            $model = PotentialCustomer::findOrFail($id);
            $model->update($request->all());
            $model->updated_by = $currentUserId;
            $model->updated_at = Carbon::now();
            $model->save();
        }else {
            $model = PotentialCustomer::create($request->all());
            $model->created_by = $currentUserId;
            $model->created_at = Carbon::now();
            $model->save();
        }
        if($salesProgresses = $request->salesProgresses) {
            $model->salesProgresses()->detach();
            foreach($salesProgresses as $index => $salesProgress) {
                if($salesProgress === true) {
                    $sales = SalesProgress::findOrFail($index);
                    $model->salesProgresses()->attach($sales);
                }
            }
        }
    }

    // upload attachments file()
    public function storePotentialCustomerAttachment($potential_customer_id)
    {
        $potentialCustomer = PotentialCustomer::findOrFail($potential_customer_id);
        $potentialCustomer->updated_by = auth()->user()->id;
        $potentialCustomer->updated_at = Carbon::now();
        $potentialCustomer->save();
        // dd(request()->all());
        if($images = request()->file('images')){
            foreach($images as $image) {
                // dd($image);
                $name = (Carbon::now()->format('dmYHi')).$image->getClientOriginalName();
                $image->move('potential_customer/'.$potentialCustomer->id.'/', $name);
                $potentialCustomerAttachment = new PotentialCustomerAttachment;
                $potentialCustomerAttachment->url = '/potential_customer/'.$potentialCustomer->id.'/'.$name;
                $potentialCustomerAttachment->potential_customer_id = $potentialCustomer->id;
                $potentialCustomerAttachment->save();
            }
        }
    }

    // return performance api
    public function getPerformanceApi(Request $request)
    {
        $model = PotentialCustomer::with(['accountManager', 'custcategory', 'creator', 'updater'])
                                ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'potential_customers.account_manager_id');

        // set update from and to & create from and to
        if($currentMonth = request('current_month')) {
            $thisMonth = Carbon::createFromFormat('d-m-Y', '01-'.$currentMonth);
            $dateFrom = $thisMonth->copy()->startOfMonth()->toDateString();
            $dateTo = $thisMonth->copy()->endOfMonth()->toDateString();
        }

        $createdQuery = clone $model;
        $updatedQuery = clone $model;

        $request->merge(['created_from' => $dateFrom]);
        $request->merge(['created_to' => $dateTo]);
        $request->merge(['updated_from' => null]);
        $request->merge(['updated_to' => null]);
        $createdQuery = $this->potentialCustomerFilter($createdQuery, $request);

        $request->merge(['created_from' => null]);
        $request->merge(['created_to' => null]);
        $request->merge(['updated_from' => $dateFrom]);
        $request->merge(['updated_to' => $dateTo]);
        $updatedQuery = $this->potentialCustomerFilter($updatedQuery, $request);

        $createdQuery = $createdQuery->select(
            'account_manager.id AS account_manager_id', 'account_manager.name AS account_manager_name',
            DB::raw('COUNT(potential_customers.id) AS created_count'),
            DB::raw('MONTH(potential_customers.created_at) AS month'),
            DB::raw('DATE(potential_customers.created_at) AS date'),
            DB::raw('DATE_FORMAT(potential_customers.created_at, "%a") AS day')
        );

        $updatedQuery = $updatedQuery->select(
            'account_manager.id AS account_manager_id', 'account_manager.name AS account_manager_name',
            DB::raw('COUNT(potential_customers.id) AS updated_count'),
            DB::raw('MONTH(potential_customers.updated_at) AS month'),
            DB::raw('DATE(potential_customers.updated_at) AS date'),
            DB::raw('DATE_FORMAT(potential_customers.updated_at, "%a") AS day')
        );

        $createdQuery = $createdQuery->groupBy('date')->groupBy('account_manager.id');
        $updatedQuery = $updatedQuery->groupBy('date')->groupBy('account_manager.id');

        if($sortName = request('sortName')){
            $createdQuery = $createdQuery->orderBy($sortName, request('sortBy') ? 'asc' : 'desc');
            $updatedQuery = $updatedQuery->orderBy($sortName, request('sortBy') ? 'asc' : 'desc');
        }else {
            $createdQuery = $createdQuery->orderBy('date', 'desc')->orderBy('account_manager.name', 'asc');
            $updatedQuery = $updatedQuery->orderBy('date', 'desc')->orderBy('account_manager.name', 'asc');
        }

        $createdCol = $createdQuery->get();
        $updatedCol = $updatedQuery->get();

        $dataArr = [
            'title' => 'Current Month',
            'month' => $thisMonth->copy()->month,
            'dates' => []
        ];
        // dd($createdCol->toArray(), $updatedCol->toArray());

        if($createdCol or $updatedCol) {
            $createdTotal = 0;
            $updatedTotal = 0;
            if(count($createdCol) > 0) {
                foreach($createdCol as $created) {
                    $addNewDate = true;
                    if($dataArr['dates']) {
                        foreach($dataArr['dates'] as $dateIndex => $date) {
                            if($dateIndex == $created->date) {
                                foreach($date as $managerIndex => $manager) {
                                    if($managerIndex == $created->account_manager_id) {
                                        $dataArr['dates'][$created->date][$created->account_manager_id]['created'] = $created->created_count;
                                        $dataArr['dates'][$created->date][$created->account_manager_id]['account_manager_name'] = $created->account_manager_name;
                                        $dataArr['dates'][$created->date][$created->account_manager_id]['date'] = $created->date;
                                        $dataArr['dates'][$created->date][$created->account_manager_id]['day'] = $created->day;
                                        $createdTotal += $created->created_count;
                                        $addNewDate = false;
                                    }
                                }
                            }
                        }
                    }

                    if($addNewDate) {
                        $dataArr['dates'][$created->date][$created->account_manager_id]['created'] = $created->created_count;
                        $dataArr['dates'][$created->date][$created->account_manager_id]['account_manager_name'] = $created->account_manager_name;
                        $dataArr['dates'][$created->date][$created->account_manager_id]['date'] = $created->date;
                        $dataArr['dates'][$created->date][$created->account_manager_id]['day'] = $created->day;
                        $createdTotal += $created->created_count;
                    }
                }

                foreach($updatedCol as $updated) {
                    $addNewDate = true;
                    if($dataArr['dates']) {
                        foreach($dataArr['dates'] as $dateIndex => $date) {
                            if($dateIndex == $updated->date) {
                                foreach($date as $managerIndex => $manager) {
                                    if($managerIndex == $updated->account_manager_id) {
                                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['updated'] = $updated->updated_count;
                                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['account_manager_name'] = $updated->account_manager_name;
                                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['date'] = $updated->date;
                                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['day'] = $updated->day;
                                        $updatedTotal += $updated->updated_count;
                                        $addNewDate = false;
                                    }
                                }
                            }
                        }
                    }

                    if($addNewDate) {
                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['updated'] = $updated->updated_count;
                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['account_manager_name'] = $updated->account_manager_name;
                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['date'] = $updated->date;
                        $dataArr['dates'][$updated->date][$updated->account_manager_id]['day'] = $updated->day;
                        $updatedTotal += $updated->updated_count;
                    }
                }
            }
            $dataArr['createdTotal'] = $createdTotal;
            $dataArr['updatedTotal'] = $updatedTotal;
        }

        return $dataArr;
    }

    // return potential customer attachments by potential customer id
    public function getAttachmentApi($id)
    {
        $potentialCustomerAttachments = PotentialCustomerAttachment::where('potential_customer_id', $id)->oldest()->paginate(1);

        return [
            'data' => $potentialCustomerAttachments
        ];
    }

    // delete single entry potential customer attachment
    public function deletePotentialCustomerAttachment($potentialCustomerAttachmentId)
    {
        $potentialCustomerAttachment = PotentialCustomerAttachment::findOrFail($potentialCustomerAttachmentId);
        File::delete($potentialCustomerAttachment->url);
        $potentialCustomerAttachment->delete();
    }

    // potentialCustomerFilter
    private function potentialCustomerFilter($query, $request)
    {
        $custcategory = $request->custcategory;
        $name = $request->name;
        $account_manager = $request->account_manager;
        $contact = $request->contact;
        $created_at = $request->created_at;
        $updated_at = $request->updated_at;
        $created_from = $request->created_from;
        $created_to = $request->created_to;
        $updated_from = $request->updated_from;
        $updated_to = $request->updated_to;

        if($custcategory) {
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            $query = $query->whereIn('custcategory_id', $custcategory);
        }

        if($name) {
            $query = $query->where('potential_customers.name', 'LIKE', '%'.$name.'%');
        }

        if($account_manager) {
            $query = $query->where('account_manager_id', $account_manager);
        }

        if($contact) {
            $query = $query->where('contact', 'LIKE', '%'.$contact.'%');
        }

        if($created_at) {
            $query = $query->whereDate('potential_customers.created_at', '=', $created_at);
        }

        if($updated_at) {
            $query = $query->whereDate('potential_customers.updated_at', '=', $updated_at);
        }

        if($created_from and $created_to) {
            $query = $query->whereDate('potential_customers.created_at', '>=', $created_from);
            $query = $query->whereDate('potential_customers.created_at', '<=', $created_to);
        }

        if($updated_from and $updated_to) {
            $query = $query->whereDate('potential_customers.updated_at', '>=', $updated_from);
            $query = $query->whereDate('potential_customers.updated_at', '<=', $updated_to);
        }

        return $query;
    }
}
