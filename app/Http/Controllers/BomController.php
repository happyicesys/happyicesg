<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Bomcategory;
use App\Bomcomponent;
use App\Bompart;
use App\Bomtemplate;
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
        // find out the descendant and delete
        $bomcomponents = Bomcomponent::where('bomcategory_id', $bomcategory->id)->get();
        if(count($bomcomponents) > 0) {
            foreach($bomcomponents as $bomcomponent) {
                $bomparts = Bompart::where('bomcomponent_id', $bomcomponent->id)->get();
                if(count($bomparts) > 0) {
                    foreach($bomparts as $bompart) {
                        $bompart->delete();
                    }
                }
                $bomcomponent->delete();
            }
        }
        $bomcategory->delete();
    }

    // creating new bom components entries(int category_id)
    public function createComponentApi($category_id)
    {
        $components = request('components');

        foreach($components as $component) {
            if($component['name']) {
                Bomcomponent::create([
                    'component_id' => $this->getBomcomponentIncrement(),
                    'name' => $component['name'],
                    'remark' => $component['remark'],
                    'bomcategory_id' => $category_id,
                    'updated_by' => auth()->user()->id,
                ]);
            }
        }
    }

    // retrieve category and bomcomponents index api()
    public function getCategoryComponentsApi()
    {
        $components = Bomcategory::with(['bomcomponents', 'bomcomponents.updater'])
                                    ->has('bomcomponents');

        // reading whether search input is filled
        if(request('category_id') or request('category_name') or request('component_id') or request('component_name')){
            $components = $this->searchComponentDBFilter($components);
        }

        if(request('sortName')){
            $components = $components->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $components = $components->latest();
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if($pageNum == 'All'){
            $components = $components->get();
        }else{
            $components = $components->paginate($pageNum);
        }

        $data = [
            'components' => $components
        ];

        return $data;
    }

    // remove individual category entry(int id)
    public function destroyComponentApi($id)
    {
        $bomcomponent = Bomcomponent::findOrFail($id);
        $bomparts = Bompart::where('bomcomponent_id', $bomcomponent->id)->get();
        if(count($bomparts) > 0) {
            foreach($bomparts as $bompart) {
                $bompart->delete();
            }
        }
        $bomcomponent->delete();
    }

    // retrieve categories by given category id(int category_id)
    public function getComponentsByCategory($category_id)
    {
        $bomcomponents = Bomcomponent::where('bomcategory_id', $category_id)->get();
        return $bomcomponents;
    }

    // creating new bom parts entries(int component_id)
    public function createPartApi($component_id)
    {
        $parts = request('parts');
        // die(var_dump(request('parts')));
        foreach($parts as $part) {
            if($part['name']) {
                Bompart::create([
                    'part_id' => $this->getBompartIncrement(),
                    'name' => $part['name'],
                    'remark' => $part['remark'],
                    'bomcomponent_id' => $component_id,
                    'updated_by' => auth()->user()->id,
                ]);
            }
        }
    }

    // retrieve parts api ()
    public function getPartsApi()
    {
        $bomparts = DB::table('bomparts')
                            ->leftJoin('users', 'users.id', '=', 'bomparts.updated_by')
                            ->leftJoin('bomcomponents', 'bomcomponents.id', '=', 'bomparts.bomcomponent_id')
                            ->leftJoin('bomcategories', 'bomcategories.id', '=', 'bomcomponents.bomcategory_id')
                            ->select(
                                'bomparts.id', 'bomparts.part_id', 'bomparts.name AS bompart_name', 'bomparts.remark AS bompart_remark', 'bomparts.thumbnail_url',
                                'bomcomponents.component_id', 'bomcomponents.name AS bomcomponent_name', 'bomcomponents.remark AS bomcomponent_remark',
                                'bomcategories.category_id', 'bomcategories.name AS bomcategory_name', 'bomcategories.remark AS bomcategory_remark',
                                'users.name AS updater'
                            );
        // reading whether search input is filled
        if(request('category_id') or request('category_name') or request('component_id') or request('component_name') or request('part_id') or request('part_name')){
            $bomparts = $this->searchPartDBFilter($bomparts);
        }

        if(request('sortName')){
            $bomparts = $bomparts->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $bomparts = $bomparts->latest('bomparts.created_at');
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if($pageNum == 'All'){
            $bomparts = $bomparts->get();
        }else{
            $bomparts = $bomparts->paginate($pageNum);
        }

        $data = [
            'bomparts' => $bomparts
        ];

        return $data;
    }

    // remove individual part entry(int id)
    public function destroyPartApi($id)
    {
        $bompart = Bompart::findOrFail($id);
        $bompart->delete();
    }

    // retrieve category and bomcomponents index api()
    public function getBomtemplateApi()
    {
        $bomtemplates = DB::table('bomtemplates')
                            ->leftJoin('bomparts', 'bomparts.id', '=', 'bomtemplates.bompart_id')
                            ->leftJoin('bomcomponents', 'bomcomponents.id', '=', 'bomparts.bomcomponent_id')
                            ->leftJoin('bomcategories', 'bomcategories.id', '=', 'bomcomponents.bomcategory_id')
                            ->leftJoin('custcategories', 'custcategories.id', '=', 'bomtemplates.custcategory_id')
                            ->leftJoin('users', 'users.id', '=', 'bomtemplates.updated_by')
                            ->select(
                                'bomtemplates.id',
                                'bomcategories.id AS bomcategory_id', 'bomcategories.category_id AS category_id', 'bomcategories.name AS bomcategory_name',
                                'bomcomponents.id AS bomcomponent_id', 'bomcomponents.component_id AS component_id', 'bomcomponents.name AS bomcomponent_name',
                                'bomparts.id AS bompart_id', 'bomparts.part_id AS part_id', 'bomparts.name AS bompart_name', 'bomcomponents.bomcategory_id AS bompart_bomcategory_id',
                                'users.name AS updated_by'
                            );

        // reading whether search input is filled
        $bomtemplates = $this->searchBomtemplateDBFilter($bomtemplates);

        $bomtemplates = $bomtemplates->orderBy('bomcategories.category_id', 'bomcomponents.component_id');

        $dataArr = [];

        $bomcategoryQuery = clone $bomtemplates;
        $bomcategoryCollection = $bomcategoryQuery->groupBy('bomcategories.id')->get();

        $bompartQuery = clone $bomtemplates;
        $bompartCollection = $bompartQuery->get();

        foreach($bomcategoryCollection as $bomcategory) {
            $partsArr = [];
            foreach($bompartCollection as $part) {
                if($part->bompart_bomcategory_id == $bomcategory->id) {
                    array_push($partsArr, [
                        'bomcomponent_name' => $part->bomcomponent_name,
                        'bompart_name' => $part->bompart_name,
                        'updated_by' => $part->updated_by,
                        'id' => $part->id
                    ]);
                }
            }
            $data = [
                'bomcategory_name' => $bomcategory->bomcategory_name,
                'category_id' => $bomcategory->category_id,
                'parts' => $partsArr,
            ];
            array_push($dataArr, $data);
        }
/*
        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if($pageNum == 'All'){
            $bomtemplates = $bomtemplates->get();
        }else{
            $bomtemplates = $bomtemplates->paginate($pageNum);
        }*/

        $data = [
            'bomtemplates' => $dataArr
        ];

        return $data;
    }

    // creating new bom templates entries(int custcategory_id)
    public function createBomtemplateApi($custcategory_id)
    {
        $bompart_id = request('bompart_id');

        Bomtemplate::create([
            'custcategory_id' => $custcategory_id,
            'bompart_id' => $bompart_id,
            'updated_by' => auth()->user()->id
        ]);
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

    // pass value into filter search for components DB (collection) [query]
    private function searchComponentDBFilter($bomcomponents)
    {
        $category_id = request('category_id');
        $category_name = request('category_name');
        $component_id = request('component_id');
        $component_name = request('component_name');

        if($category_id){
            $bomcomponents = $bomcomponents->where('category_id', 'LIKE', '%'.$category_id.'%');
        }
        if($category_name){
            $bomcomponents = $bomcomponents->where('name', 'LIKE', '%'.$category_name.'%');
        }
        if($component_id){
            $bomcomponents = $bomcomponents->whereHas('bomcomponents', function($query) use ($component_id){
                $query->where('component_id', 'LIKE', '%'.$component_id.'%');
            });
        }
        if($component_name){
            $bomcomponents = $bomcomponents->whereHas('bomcomponents', function($query) use ($component_name){
                $query->where('name', 'LIKE', '%'.$component_name.'%');
            });
        }
        return $bomcomponents;
    }

    // pass value into filter search for parts DB (collection) [query]
    private function searchPartDBFilter($bomparts)
    {
        $category_id = request('category_id');
        $category_name = request('category_name');
        $component_id = request('component_id');
        $component_name = request('component_name');
        $part_id = request('part_id');
        $part_name = request('part_name');

        if($category_id){
            $bomparts = $bomparts->where('bomcategories.category_id', 'LIKE', '%'.$category_id.'%');
        }
        if($category_name){
            $bomparts = $bomparts->where('bomcategories.name', 'LIKE', '%'.$category_name.'%');
        }
        if($component_id){
            $bomparts = $bomparts->where('bomcomponents.component_id', 'LIKE', '%'.$component_id.'%');
        }
        if($component_name){
            $bomparts = $bomparts->where('bomcomponents.name', 'LIKE', '%'.$component_name.'%');
        }
        if($part_id){
            $bomparts = $bomparts->where('bomparts.part_id', 'LIKE', '%'.$part_id.'%');
        }
        if($part_name){
            $bomparts = $bomparts->where('bomparts.name', 'LIKE', '%'.$part_name.'%');
        }
        return $bomparts;
    }

    // pass value into filter search for parts DB (collection, int custcategory_id)
    private function searchBomtemplateDBFilter($bomtemplates)
    {
        $custcategory_id = request('custcategory_id');

        $bomtemplates = $bomtemplates->where('custcategories.id', '=', $custcategory_id);

        return $bomtemplates;
    }
}
