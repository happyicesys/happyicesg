<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Bomcategory;
use App\Bomcomponent;
use App\Bompart;
use DB;
use App\GetIncrement;

class BomController extends Controller
{
    use GetIncrement;

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return bom index page
    public function index()
    {
        return view('bom.index');
    }

    // retrieve all of the bom categories
    public function getCategoriesApi()
    {
        $bomcategories = DB::table('bomcategories')
                            ->leftJoin('users', 'users.id', '=', 'bomcategories.updated_by')
                            ->select(
                                'bomcategories.id', 'bomcategories.category_id', 'bomcategories.name', 'bomcategories.remark',
                                'users.name AS updater'
                            );
        // reading whether search input is filled
        if(request('category_id') or request('name')){
            $bomcategories = $this->searchDBFilter($bomcategories);
        }

        if(request('sortName')){
            $bomcategories = $bomcategories->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $bomcategories = $bomcategories->latest('bomcategories.created_at');
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if($pageNum == 'All'){
            $bomcategories = $bomcategories->get();
        }else{
            $bomcategories = $bomcategories->paginate($pageNum);
        }

        $data = [
            'bomcategories' => $bomcategories
        ];

        return $data;
    }

    // creating new bom category entry()
    public function createCategoryApi()
    {
        $name = request('name');
        $remark = request('remark');

        Bomcategory::create([
            'category_id' => $this->getBomcategoryIncrement(),
            'name' => $name,
            'remark' => $remark,
            'updated_by' => auth()->user()->id,
        ]);
    }

    // remove individual category entry(int id)
    public function destroyCategoryApi($id)
    {
        $bomcategory = Bomcategory::findOrFail($id);
        $bomcategory->delete();
    }

    // pass value into filter search for DB (collection) [query]
    private function searchDBFilter($bomcategories)
    {
        if(request('category_id')){
            $bomcategories = $bomcategories->where('bomcategories.category_id', 'LIKE', '%'.request('category_id').'%');
        }
        if(request('name')){
            $bomcategories = $bomcategories->where('bomcategories.name', 'LIKE', '%'.request('name').'%');
        }
        if(request('sortName')){
            $bomcategories = $bomcategories->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $bomcategories;
    }
}
