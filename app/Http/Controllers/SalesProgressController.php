<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\SalesProgress;

class SalesProgressController extends Controller
{
    public function getDataApi(Request $request)
    {
        $model = SalesProgress::with(['potentialCustomers']);

        // $model = $this->potentialCustomerFilter($model, $request);

        if($request->sortName) {
            $model = $model->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }else {
            $model = $model->orderBy('order', 'asc');
        }

        $pageNum = $request->pageNum ? $request->pageNum : 100;

        if ($pageNum == 'All') {
            $model = $model->get();
        } else {
            $model = $model->paginate($pageNum);
        }

        return [
            'data' => $model
        ];
    }
}
