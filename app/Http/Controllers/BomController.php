<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Http\Requests;
use App\Bomcategory;
use App\Bomcomponent;
use App\Bompart;
use App\Bomtemplate;
use App\Bomvending;
use App\Bommaintenance;
use App\Bompartconsumable;
use App\Person;
use App\Bomcategorycustcat;
use App\Bomcomponentcustcat;
use App\Bompartcustcat;
use App\Bompartconsumablecustcat;
use App\Bomgroup;
use DB;
use App\GetIncrement;
use Carbon\Carbon;

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
        $bomcategories = Bomcategory::with(['updater', 'bomcategorycustcat.custcategory' => function($query) {
            $query->orderBy('name', 'asc');
        }]);
/*        $bomcategories = DB::table('bomcategories')
                            ->leftJoin('users', 'users.id', '=', 'bomcategories.updated_by')
                            ->select(
                                'bomcategories.id', 'bomcategories.category_id', 'bomcategories.name', 'bomcategories.remark', 'bomcategories.drawing_id', 'bomcategories.drawing_path',
                                'users.name AS updater'
                            );*/
        // reading whether search input is filled
        if(request('category_id') or request('name')){
            $bomcategories = $this->searchBomcategoryFilter($bomcategories);
        }

        if(request('sortName')){
            $bomcategories = $bomcategories->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $bomcategories = $bomcategories->orderBy('category_id');
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
        $components = Bomcategory::with(array(
            'bomcategorycustcat.custcategory' => function($query) {
                $query->orderBy('name', 'asc');
            },
            'bomcomponents.bomcomponentcustcat.custcategory' => function($query) {
                $query->orderBy('name', 'asc');
            },
            'bomcomponents' => function($query) {
                $query->orderBy('component_id', 'ASC');
            },
            'bomcomponents.updater',
            'bomcomponents.bomparts' => function($query) {
                $query->orderBy('part_id', 'ASC');
            },
            'bomcomponents.bomparts.bomgroup',
            'bomcomponents.bomparts.bompartconsumables',
            'bomcomponents.bomparts.bompartconsumables.bomgroup',
            'bomcomponents.bomparts.bompartconsumables.updater',
            'bomcomponents.bomparts.bompartconsumables.bompartconsumablecustcat.custcategory',
            'bomcomponents.bomparts.updater',
            'bomcomponents.bomparts.bomtemplates.custcategory' => function($query) {
                $query->orderBy('name', 'ASC');
            }))->has('bomcomponents');

        // reading whether search input is filled
        if(request('category_id') or request('category_name') or request('component_id') or request('component_name') or request('custcategory')){
            $components = $this->searchComponentDBFilter($components);
        }

        if(request('sortName')){
            $components = $components->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $components = $components->orderBy('category_id', 'asc');
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
        $bompart->bomtemplates()->delete();
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

        $bomtemplates = $bomtemplates->orderBy('bomcategories.category_id', 'asc')->orderBy('bomcomponents.component_id', 'asc');

        $dataArr = [];

        $bomcategoryQuery = clone $bomtemplates;
        $bomcategoryCollection = $bomcategoryQuery->groupBy('bomcategories.id')->get();

        $bompartQuery = clone $bomtemplates;
        $bompartCollection = $bompartQuery->get();

        foreach($bomcategoryCollection as $bomcategory) {
            $partsArr = [];
            foreach($bompartCollection as $part) {
                if($part->bompart_bomcategory_id == $bomcategory->bomcategory_id) {
                    array_push($partsArr, [
                        'bomcomponent_name' => $part->bomcomponent_name,
                        'bompart_name' => $part->bompart_name,
                        'updated_by' => $part->updated_by,
                        'id' => $part->id,
                        'bompart_bomcategory_id' => $part->bompart_bomcategory_id,
                        'bomcategory_id' => $bomcategory->id,
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
            'bomcomponent_id' => Bompart::findOrFail($bompart_id)->bomcomponent->id,
            'updated_by' => auth()->user()->id
        ]);
    }

    // remove template binding in definition(int bomtemplate_id)
    public function destroyTemplateApi($bomtemplate_id)
    {
        $bomtemplate = Bomtemplate::findOrFail($bomtemplate_id);
        $bomtemplate->delete();
    }

    // syncing the template to bomvending via custcategory id()
    public function syncTemplateVending()
    {
        $custcategory_id = request('custcategory_id');

        if($custcategory_id == 'All') {
            $custcatArr = [];
            $bomtemplates = Bomtemplate::all();
            foreach($bomtemplates as $bomtemplate) {
                if(! in_array($bomtemplate->custcategory_id, $custcatArr)) {
                    array_push($custcatArr, $bomtemplate->custcategory_id);
                }
            }
            if(count($custcatArr)>0) {
                $people = Person::whereIn('custcategory_id', $custcatArr)->get();

                foreach($people as $person) {
                    if($person->bomvendings) {
                        $person->bomvendings()->delete();
                    }
                    foreach($bomtemplates as $bomtemplate) {
                        if($person->custcategory_id == $bomtemplate->custcategory_id) {
                            Bomvending::create([
                                'custcategory_id' => $bomtemplate->custcategory_id,
                                'bomcomponent_id' => $bomtemplate->bompart->bomcomponent->id,
                                'bompart_id' => $bomtemplate->bompart_id,
                                'person_id' => $person->id,
                                'updated_by' => auth()->user()->id
                            ]);
                        }
                    }
                }
            }

        }else {
            $bomtemplates = Bomtemplate::where('custcategory_id', $custcategory_id)->get();
            $people = Person::where('custcategory_id', $custcategory_id)->get();

            foreach($people as $person) {
                if($person->bomvendings) {
                    $person->bomvendings()->delete();
                }
                foreach($bomtemplates as $bomtemplate) {
                    Bomvending::create([
                        'custcategory_id' => $custcategory_id,
                        'bomcomponent_id' => $bomtemplate->bompart->bomcomponent->id,
                        'bompart_id' => $bomtemplate->bompart_id,
                        'person_id' => $person->id,
                        'updated_by' => auth()->user()->id
                    ]);
                }
            }
        }
    }

    // retrieve people who are dvm or fvm()
    public function getVendingsPeople()
    {
        $people = Person::with('custcategory')->where('is_vending', 1)->orWhere('is_dvm', 1)->orderBy('cust_id', 'asc')->get();

        return $people;
    }

    // retrieve bomvendings index api()
    public function getVendingsPeopleApi()
    {
        $bomvendings = DB::table('bomvendings')
                            ->leftJoin('people', 'people.id', '=', 'bomvendings.person_id')
                            ->leftJoin('bomparts', 'bomparts.id', '=', 'bomvendings.bompart_id')
                            ->leftJoin('bomcomponents', 'bomcomponents.id', '=', 'bomparts.bomcomponent_id')
                            ->leftJoin('bomcategories', 'bomcategories.id', '=', 'bomcomponents.bomcategory_id')
                            ->leftJoin('custcategories', 'custcategories.id', '=', 'bomvendings.custcategory_id')
                            ->leftJoin('bomtemplates', function($join) {
                                $join->on('bomtemplates.bompart_id', '=', 'bomparts.id');
                                $join->on('bomtemplates.custcategory_id', '=', 'custcategories.id');
                            })
                            ->leftJoin('users', 'users.id', '=', 'bomvendings.updated_by')
                            ->select(
                                'bomvendings.id',
                                'people.id AS person_id', 'people.cust_id', 'people.company',
                                'bomcategories.id AS bomcategory_id', 'bomcategories.category_id AS category_id', 'bomcategories.name AS bomcategory_name',
                                'bomcomponents.id AS bomcomponent_id', 'bomcomponents.component_id AS component_id', 'bomcomponents.name AS bomcomponent_name',
                                'bomparts.id AS bompart_id', 'bomparts.part_id AS part_id', 'bomparts.name AS bompart_name', 'bomcomponents.bomcategory_id AS bompart_bomcategory_id',
                                'custcategories.name AS custcategory_name', 'custcategories.id AS custcategory_id',
                                'users.name AS updated_by'
                            );


        $bomvendings = $this->searchVendingDBFilter($bomvendings);

        $dataArr = [];

        $peopleQuery = clone $bomvendings;
        $peopleCollection = $peopleQuery->groupBy('people.id')->get();

        $bomcategoryQuery = clone $bomvendings;
        $bomcategoryCollection = $bomcategoryQuery->groupBy('bomcategories.id')->get();

        $bomcomponentQuery = clone $bomvendings;
        $bomcomponentCollection = $bomcomponentQuery->orderBy('bomcategories.category_id', 'asc')->orderBy('bomcomponents.component_id', 'asc')->groupBy('bomcomponents.id')->get();

            $dataArr = [];
            foreach($bomcomponentCollection as $indexcomponent => $component) {
                foreach($peopleCollection as $indexperson => $person) {
                    $component_name = $component->bomcomponent_name;
                    $bomvending = Bomvending::where('bomcomponent_id', $component->bomcomponent_id)
                                ->where('person_id', $person->person_id)
                                ->first();
                    $part = $bomvending ? $bomvending->bompart->name : null;
                    $part_id = $bomvending ? $bomvending->bompart->id : null;


                    $bomtemplate = Bomtemplate::where('bomcomponent_id', $component->bomcomponent_id)
                                    ->where('custcategory_id', $person->custcategory_id)
                                    ->first();
                    $original_part = $bomtemplate ? $bomtemplate->bompart->name : null;
                    $original_part_id = $bomtemplate ? $bomtemplate->bompart->id : null;
                    $color = $part_id == $original_part_id ? '': 'red';
                    $choices = Bompart::where('bomcomponent_id', $component->bomcomponent_id)->whereNotIn('id', [$part_id])->get();

                    $dataArr[$indexcomponent][$indexperson] = [
                        'vending_id' => $bomvending ? $bomvending->id : null,
                        'part' => $part,
                        'part_id' => $part_id,
                        'original_part' => $original_part,
                        'original_part_id' => $original_part_id,
                        'component_id' => $component->bomcomponent_id,
                        'category_id' => $component->bomcategory_id,
                        'color' => $color,
                        'custcategory_id' => $person->custcategory_id,
                        'choices' => $choices
                    ];
                }
            }

        $data = [
            'people' => $peopleCollection,
            'bomcategories' => $bomcategoryCollection,
            'bomcomponents' => $bomcomponentCollection,
            'bomvendings' => $dataArr,
        ];

        return $data;
    }

    // change the bom vending part ()
    public function changeBomvendingPart()
    {
        $vending_id = request('vending_id');
        $part_id = request('part_id');
        $bomvending = Bomvending::findOrFail($vending_id);
        $bomvending->bompart_id = $part_id;
        $bomvending->save();
    }

    // bind custcategory to bom part
    public function bindBompartCustcat()
    {
        // die(var_dump(request('custcategory_id')['custcategory_id']));
        $bompart_id = request('bompart_id');
        $custcategory_id = request('custcategory_id')['custcategory_id'];
        $bomtemplate = Bomtemplate::where('bompart_id', $bompart_id)->where('custcategory_id', $custcategory_id)->first();

        if($bomtemplate) {
            $bomtemplate->delete();
        }else {
            if($custcategory_id) {
                Bomtemplate::create([
                    'custcategory_id' => $custcategory_id,
                    'bompart_id' => $bompart_id,
                    'bomcomponent_id' => Bompart::where('id', $bompart_id)->first()->bomcomponent->id,
                    'updated_by' => auth()->user()->id
                ]);
            }
        }
    }

    // bind custcategory to bom category
    public function bindBomcategoryCustcat()
    {
        $bomcategory_id = request('bomcategory_id');
        $custcategory_id = request('custcategory_id');
        $bomcategorycustcat = Bomcategorycustcat::where('bomcategory_id', $bomcategory_id)->where('custcategory_id', $custcategory_id)->first();

        if($bomcategorycustcat) {
            $bomcategorycustcat->delete();
        }else {
            if($custcategory_id) {
                Bomcategorycustcat::create([
                    'custcategory_id' => $custcategory_id,
                    'bomcategory_id' => $bomcategory_id,
                    'updated_by' => auth()->user()->id
                ]);
            }
        }
    }

    // bind custcategory to bom component
    public function bindBomcomponentCustcat()
    {
        // die(var_dump(request('custcategory_id')));
        $bomcomponent_id = request('bomcomponent_id');
        $custcategory_id = request('custcategory_id')['custcategory_id'];
        $bomcomponentcustcat = Bomcomponentcustcat::where('bomcomponent_id', $bomcomponent_id)->where('custcategory_id', $custcategory_id)->first();

        if($bomcomponentcustcat) {
            $bomcomponentcustcat->delete();
        }else {
            if($custcategory_id) {
                Bomcomponentcustcat::create([
                    'custcategory_id' => $custcategory_id,
                    'bomcomponent_id' => $bomcomponent_id,
                    'updated_by' => auth()->user()->id
                ]);
            }
        }
    }

    // pass value into filter search for DB (collection) [query]
    private function searchBomcategoryFilter($bomcategories)
    {
        if(request('category_id')){
            $bomcategories = $bomcategories->where('category_id', 'LIKE', '%'.request('category_id').'%');
        }
        if(request('name')){
            $bomcategories = $bomcategories->where('name', 'LIKE', '%'.request('name').'%');
        }
        if(request('sortName')){
            $bomcategories = $bomcategories->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $bomcategories;
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
        $custcategory = request('custcategory');

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
        if($custcategory) {
            if(count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
/*            $bomcomponents = $bomcomponents->with(['bomcategorycustcat.custcategory' => function($query) use ($custcategory) {
                $query->whereIn('id', $custcategory);
            }]);*/
            $bomcomponents = $bomcomponents->whereHas('bomcategorycustcat.custcategory', function($query) use ($custcategory){
                $query->whereIn('id', $custcategory);
            });
/*            $bomcomponents = $bomcomponents->whereHas('bomcomponents.bomcomponentcustcat.custcategory', function($query) use ($custcategory){
                $query->whereIn('id', $custcategory);
            });
            $bomcomponents = $bomcomponents->whereHas('bomcomponents.bomparts.bompartconsumables.bompartconsumablecustcat.custcategory', function($query) use ($custcategory){
                $query->whereIn('id', $custcategory);
            });
            $bomcomponents = $bomcomponents->whereHas('bomcomponents.bomparts.bomtemplates.custcategory', function($query) use ($custcategory){
                $query->whereIn('id', $custcategory);
            });*/
        }
        return $bomcomponents;
    }

    // retrieve templates option by providing bomcomponent id and custcategory id(int component_id, int custcategory_id)
    public function getTemplateByComponentCustcategory($bomcomponent_id, $custcategory_id)
    {
        $bomtemplates = Bomtemplate::where('bomcomponent_id', $bomcomponent_id)->where('custcategory_id', $custcategory_id)->get();
        return $bomtemplates;
    }

    // retrieve all of the bom maintenance records()
    public function getBommaintenancesApi()
    {
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $bommaintenances = DB::table('bommaintenances AS x')
                        ->leftJoin('people', 'x.person_id', '=', 'people.id')
                        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                        ->leftJoin('users AS technicians', 'technicians.id', '=', 'x.technician_id')
                        ->leftJoin('bomcomponents', 'bomcomponents.id', '=', 'x.bomcomponent_id')
                        ->leftJoin('bomcategories', 'bomcategories.id', '=', 'bomcomponents.bomcategory_id')
                        ->leftJoin('users AS creators', 'creators.id', '=', 'x.created_by')
                        ->leftJoin('users AS updaters', 'updaters.id', '=', 'x.updated_by')
                        ->select(
                                    'x.id',
                                    'x.maintenance_id',
                                    DB::raw('DATE(x.datetime) AS date'),
                                    DB::raw('TIME_FORMAT(TIME(x.datetime), "%h:%i %p") AS time'),
                                    'x.time_spend', 'x.urgency', 'x.issue_type', 'x.solution', 'x.remark',
                                    'people.cust_id', 'people.company',
                                    'custcategories.id AS custcategory_id', 'custcategories.name AS custcategory_name',
                                    'technicians.id AS technician_id', 'technicians.name AS technician_name',
                                    'bomcategories.id AS bomcategory_id', 'bomcategories.name AS bomcategory_name',
                                    'bomcomponents.id AS bomcomponent_id', 'bomcomponents.name AS bomcomponent_name',
                                    'creators.name AS creator', 'updaters.name AS updater'
                                );

        // reading whether search input is filled
        if(request('person_id') or request('date_from') or request('date_to') or request('custcategory_id') or request('technician_id') or  request('bomcomponent_id') or request('issue_type')){
            $bommaintenances = $this->searchBommaintenanceDBFilter($bommaintenances);
        }

        if(request('sortName')){
            $bommaintenances = $bommaintenances->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $bommaintenances = $bommaintenances->latest('x.datetime')->get();
        }else{
            $bommaintenances = $bommaintenances->latest('x.datetime')->paginate($pageNum);
        }

        $data = [
            'bommaintenances' => $bommaintenances,
        ];
/*
        if(request('export_excel')) {
            $this->exportFtransactionIndexExcel($data);
        }*/

        return $data;
    }

    // create entry for bom maintenance()
    public function createBommaintenanceEntry()
    {
        $person_id = request('person_id');
        $date = request('date');
        $time = request('time');
        $technician_id = request('technician_id');
        $urgency = request('urgency');
        $time_spend = request('time_spend');
        $bomcomponent_id = request('bomcomponent_id');
        $issue_type = request('issue_type');
        $solution = request('solution');
        $remark = request('remark');

        $bommaintenances = Bommaintenance::create([
            'maintenance_id' => $this->getBommaintenanceIncrement(),
            'person_id' => $person_id,
            'datetime' => $this->convertDateTimeCarbon($date, $time),
            'technician_id' => $technician_id,
            'urgency' => $urgency,
            'time_spend' => $time_spend,
            'bomcomponent_id' => $bomcomponent_id,
            'issue_type' => $issue_type,
            'solution' => $solution,
            'remark' => $remark,
            'created_by' => auth()->user()->id,
        ]);
    }

    // remove the bom maintenance api(int bommaintenance_id)
    public function destroyBommaintenanceApi($bommaintenance_id)
    {
        $bommaintenance = Bommaintenance::findOrFail($bommaintenance_id);
        $bommaintenance->delete();
    }

    // update bom part remark()
    public function updateBompartRemark()
    {
        $bompart_id = request('bompart_id');
        $remark = request('remark');

        $bompart = Bompart::findOrFail($bompart_id);

        $bompart->remark = $remark;
        $bompart->updated_by = auth()->user()->id;
        $bompart->save();
    }

    // update bom part qty()
    public function updateBompartQty()
    {
        $bompart_id = request('bompart_id');
        $qty = request('qty');

        $bompart = Bompart::findOrFail($bompart_id);

        $bompart->qty = $qty;
        $bompart->updated_by = auth()->user()->id;
        $bompart->save();
    }

    // update bom component remark()
    public function updateBomcomponentRemark()
    {
        $bomcomponent_id = request('bomcomponent_id');
        $remark = request('remark');

        $bomcomponent = Bomcomponent::findOrFail($bomcomponent_id);

        $bomcomponent->remark = $remark;
        $bomcomponent->updated_by = auth()->user()->id;
        $bomcomponent->save();
    }

    // update bom component qty()
    public function updateBomcomponentQty()
    {
        $bomcomponent_id = request('bomcomponent_id');
        $qty = request('qty');

        $bomcomponent = Bomcomponent::findOrFail($bomcomponent_id);

        $bomcomponent->qty = $qty;
        $bomcomponent->updated_by = auth()->user()->id;
        $bomcomponent->save();
    }

    // update bom part()
    public function updateBompartApi()
    {
        $id = request('id');
        $part_id = request('part_id');
        $drawing_id = request('drawing_id');
        $drawing_path = request('drawing_path');
        $name = request('name');
        $qty = request('qty');
        $remark = request('remark');
        $supplier_order = request('supplier_order');
        $unit_price = request('unit_price');
        $pic = request('pic');
        $bomgroup_id = request('bomgroup_id');

        $bompart = Bompart::findOrFail($id);
        $bompart->part_id = $part_id;
        $bompart->bomgroup_id = $bomgroup_id;
        $bompart->drawing_id = $drawing_id;
        $bompart->drawing_path = $drawing_path;
        $bompart->name = $name;
        $bompart->qty = $qty;
        $bompart->remark = $remark;
        $bompart->supplier_order = $supplier_order;
        $bompart->unit_price = $unit_price;
        $bompart->pic = $pic;
        $bompart->updated_by = auth()->user()->id;
        $bompart->save();
    }

    // create bompart via bomcomponent()
    public function createBompartByBomcomponent()
    {
        $part_id = request('part_id');
        $movable = request('movable');
        $bomcomponent_id = request('bomcomponent_id');
        $name = request('name');
        $qty = request('qty');
        $remark = request('remark');
        $supplier_order = request('supplier_order');
        $unit_price = request('unit_price');
        $pic = request('pic');
        $bomgroup_id = request('bomgroup_id');

        $bompart = Bompart::create([
            'part_id' => $part_id,
            'movable' => 0,
            'bomcomponent_id' => $bomcomponent_id,
            'bomgroup_id' => $bomgroup_id,
            'name' => $name,
            'qty' => $qty,
            'remark' => $remark,
            'updated_by' => auth()->user()->id,
            'supplier_order' => $supplier_order,
            'unit_price' => $unit_price,
            'pic' => $pic
        ]);

        $bomcomponent = Bomcomponent::findOrFail($bomcomponent_id);
        foreach($bomcomponent->bomcomponentcustcat as $custcat) {
            Bomtemplate::create([
                'custcategory_id' => $custcat->custcategory_id,
                'bomcomponent_id' => $bomcomponent->id,
                'bompart_id' => $bompart->id,
                'updated_by' => auth()->user()->id
            ]);
        }
    }

    // get bompart increment()
    public function getBompartIncrementApi()
    {
        $bompart_id = $this->getBompartIncrement();
        return $bompart_id;
    }

    // validate bompart id and return error()
    public function validateBompartId()
    {
        $this->validate(request(), [
            'part_id' => 'required|unique:bomparts,part_id'
        ],[
            'part_id.required' => 'Part ID is required',
            'part_id.unique' => 'This Part ID is in used',
        ]);
    }

    // update bom component()
    public function updateBomcomponentApi()
    {
        $id = request('id');
        $component_id = request('component_id');
        $drawing_id = request('drawing_id');
        $drawing_path = request('drawing_path');
        $name = request('name');
        $remark = request('remark');
        $supplier_order = request('supplier_order');
        $unit_price = request('unit_price');
        $pic = request('pic');

        $bomcomponent = Bomcomponent::findOrFail($id);
        $bomcomponent->component_id = $component_id;
        $bomcomponent->drawing_id = $drawing_id;
        $bomcomponent->drawing_path = $drawing_path;
        $bomcomponent->name = $name;
        $bomcomponent->remark = $remark;
        $bomcomponent->supplier_order = $supplier_order;
        $bomcomponent->unit_price = $unit_price;
        $bomcomponent->pic = $pic;
        $bomcomponent->updated_by = auth()->user()->id;
        $bomcomponent->save();
    }

    // update bom category()
    public function updateBomcategoryApi()
    {
        $id = request('id');
        $category_id = request('category_id');
        $drawing_id = request('drawing_id');
        $name = request('name');
        $remark = request('remark');

        $bomcategory = Bomcategory::findOrFail($id);
        $bomcategory->category_id = $category_id;
        $bomcategory->drawing_id = $drawing_id;
        $bomcategory->name = $name;
        $bomcategory->remark = $remark;
        $bomcategory->updated_by = auth()->user()->id;
        $bomcategory->save();
    }

    // upload category file()
    public function uploadBomcategoryDrawing($bomcategory_id)
    {
        $bomcategory = Bomcategory::findOrFail($bomcategory_id);
        $bomcategory->updated_by = auth()->user()->id;
        $bomcategory->save();
        if($file = request()->file('image_file')){
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $file->move('bom_asset/bomcategory/'.$bomcategory->id.'/', $name);
            $bomcategory->drawing_path = '/bom_asset/bomcategory/'.$bomcategory->id.'/'.$name;
            $bomcategory->save();
        }
    }

    // upload component file()
    public function uploadBomcomponentDrawing($bomcomponent_id)
    {
        $bomcomponent = Bomcomponent::findOrFail($bomcomponent_id);
        $bomcomponent->updated_by = auth()->user()->id;
        $bomcomponent->save();
        if($file = request()->file('component_file')){
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $file->move('bom_asset/bomcategory/'.$bomcomponent->bomcategory->id.'/'.$bomcomponent->id.'/', $name);
            $bomcomponent->drawing_path = '/bom_asset/bomcategory/'.$bomcomponent->bomcategory->id.'/'.$bomcomponent->id.'/'.$name;
            $bomcomponent->save();
        }
    }

    // upload part file()
    public function uploadBompartDrawing($bompart_id)
    {
        $bompart = Bompart::findOrFail($bompart_id);
        $bompart->updated_by = auth()->user()->id;
        $bompart->save();
        if($file = request()->file('part_file')){
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $file->move('bom_asset/bomcategory/'.$bompart->bomcomponent->bomcategory->id.'/'.$bompart->bomcomponent->id.'/', $name);
            $bompart->drawing_path = '/bom_asset/bomcategory/'.$bompart->bomcomponent->bomcategory->id.'/'.$bompart->bomcomponent->id.'/'.$name;
            $bompart->save();
        }
    }

    // get single bomcategory api(int bomcategory_id)
    public function getBomcategorySingleApi($bomcategory_id)
    {
        $bomcategory = Bomcategory::findOrFail($bomcategory_id);
        return $bomcategory;
    }

    // get single bomcomponent api(int bomcategory_id)
    public function getBomcomponentSingleApi($bomcomponent_id)
    {
        $bomcomponent = Bomcomponent::findOrFail($bomcomponent_id);
        return $bomcomponent;
    }

    // get single bompart api(int bompart_id)
    public function getBompartSingleApi($bompart_id)
    {
        $bompart = Bompart::findOrFail($bompart_id);
        return $bompart;
    }

    public function removeBomcategoryDrawingApi($bomcategory_id)
    {
        $bomcategory = Bomcategory::findOrFail($bomcategory_id);
        $bomcategory->updated_by = auth()->user()->id;
        $bomcategory->save();
        File::delete(public_path().$bomcategory->drawing_path);
        $bomcategory->drawing_path = null;
        $bomcategory->save();
    }

    public function removeBomcomponentDrawingApi($bomcomponent_id)
    {
        $bomcomponent = Bomcomponent::findOrFail($bomcomponent_id);
        $bomcomponent->updated_by = auth()->user()->id;
        $bomcomponent->save();
        File::delete(public_path().$bomcomponent->drawing_path);
        $bomcomponent->drawing_path = null;
        $bomcomponent->save();
    }

    // remove bompart drawing(int bompart_id)
    public function removeBompartDrawingApi($bompart_id)
    {
        $bompart = Bompart::findOrFail($bompart_id);
        $bompart->updated_by = auth()->user()->id;
        $bompart->save();
        File::delete(public_path().$bompart->drawing_path);
        $bompart->drawing_path = null;
        $bompart->save();
    }

    // add cust category into bomcategory multiple select(int bomcategory_id)
    public function addCustcatBomcategory($bomcategory_id)
    {
        $custcategory = request('custcategory');

        Bomcategorycustcat::create([
            'custcategory_id' => $custcategory['id'],
            'bomcategory_id' => $bomcategory_id,
            'updated_by' => auth()->user()->id
        ]);
    }

    // remove cust category from bomcategory multiple select(int bomcategory_id)
    public function removeCustcatBomcategory($bomcategory_id)
    {
        $custcategory = request('custcategory');

        $bomcategorycustcat = Bomcategorycustcat::where('bomcategory_id', $bomcategory_id)->where('custcategory_id', $custcategory['id'])->firstOrFail();
        $bomcategorycustcat->delete();
    }

    // get bompartconsumable increment()
    public function getBompartconsumableIncrementApi()
    {
        $bompartconsumable_id = $this->getBompartconsumableIncrement();
        return $bompartconsumable_id;
    }

    // update bom bompartconsumable remark()
    public function updateBompartconsumableRemark()
    {
        $bompartconsumable_id = request('bompartconsumable_id');
        $remark = request('remark');

        $bompartconsumable = Bompartconsumable::findOrFail($bompartconsumable_id);

        $bompartconsumable->remark = $remark;
        $bompartconsumable->updated_by = auth()->user()->id;
        $bompartconsumable->save();
    }

    // update bom bompartconsumable qty()
    public function updateBompartconsumableQty()
    {
        $bompartconsumable_id = request('bompartconsumable_id');
        $qty = request('qty');

        $bompartconsumable = Bompartconsumable::findOrFail($bompartconsumable_id);

        $bompartconsumable->qty = $qty;
        $bompartconsumable->updated_by = auth()->user()->id;
        $bompartconsumable->save();
    }

    // remove the bompartconsumable api(int bompartconsumable_id)
    public function destroyBompartconsumableApi($bompartconsumable_id)
    {
        $bompartconsumable = Bompartconsumable::findOrFail($bompartconsumable_id);
        $bompartconsumable->delete();
    }

    // create bompartconsumable()
    public function createBompartconsumable()
    {
        $partconsumable_id = request('bompartconsumable_id');
        $bompart_id = request('bompart_id');
        $name = request('name');
        $qty = request('qty');
        $remark = request('remark');
        $supplier_order = request('supplier_order');
        $unit_price = request('unit_price');
        $pic = request('pic');
        $bomgroup_id = request('bomgroup_id');

        $bompartconsumable = Bompartconsumable::create([
            'partconsumable_id' => $partconsumable_id,
            'bompart_id' => $bompart_id,
            'bomgroup_id' => $bomgroup_id,
            'name' => $name,
            'qty' => $qty,
            'remark' => $remark,
            'updated_by' => auth()->user()->id,
            'supplier_order' => $supplier_order,
            'unit_price' => $unit_price,
            'pic' => $pic
        ]);

        $bompart = Bompart::findOrFail($bompart_id);
        foreach($bompart->bomtemplates as $bomtemplate) {
            Bompartconsumablecustcat::create([
                'custcategory_id' => $bomtemplate->custcategory_id,
                'bompartconsumable_id' => $bompartconsumable->id,
                'updated_by' => auth()->user()->id
            ]);
        }
    }

    // update bompartconsumable()
    public function updateBompartconsumable()
    {
        $id = request('id');
        $bompart_id = request('bompart_id');
        $partconsumable_id = request('bompartconsumable_id');
        $drawing_id = request('drawing_id');
        $drawing_path = request('drawing_path');
        $name = request('name');
        $qty = request('qty');
        $remark = request('remark');
        $supplier_order = request('supplier_order');
        $unit_price = request('unit_price');
        $pic = request('pic');
        $bomgroup_id = request('bomgroup_id');

        $bompartconsumable = Bompartconsumable::findOrFail($id);
        $bompartconsumable->bompart_id = $bompart_id;
        $bompartconsumable->bomgroup_id = $bomgroup_id;
        $bompartconsumable->drawing_id = $drawing_id;
        $bompartconsumable->drawing_path = $drawing_path;
        $bompartconsumable->name = $name;
        $bompartconsumable->qty = $qty;
        $bompartconsumable->remark = $remark;
        $bompartconsumable->supplier_order = $supplier_order;
        $bompartconsumable->unit_price = $unit_price;
        $bompartconsumable->pic = $pic;
        $bompartconsumable->updated_by = auth()->user()->id;
        $bompartconsumable->save();
    }

    // upload bompartconsumable file()
    public function uploadBompartconsumableDrawing($bompartconsumable_id)
    {
        $bompartconsumable = Bompartconsumable::findOrFail($bompartconsumable_id);
        $bompartconsumable->updated_by = auth()->user()->id;
        $bompartconsumable->save();
        if($file = request()->file('bompartconsumable_file')){
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $file->move('bom_asset/bomcategory/'.$bompartconsumable->bompart->bomcomponent->bomcategory->id.'/'.$bompartconsumable->bompart->bomcomponent->id.'/', $name);
            $bompartconsumable->drawing_path = '/bom_asset/bomcategory/'.$bompartconsumable->bompart->bomcomponent->bomcategory->id.'/'.$bompartconsumable->bompart->bomcomponent->id.'/'.$name;
            $bompartconsumable->save();
        }
    }

    // remove bompartconsumable drawing(int bompart_id)
    public function removeBompartconsumableDrawingApi($bompartconsumable_id)
    {
        $bompartconsumable = Bompart::findOrFail($bompartconsumable_id);
        $bompartconsumable->updated_by = auth()->user()->id;
        $bompartconsumable->save();
        File::delete(public_path().$bompartconsumable->bompart->drawing_path);
        $bompartconsumable->drawing_path = null;
        $bompartconsumable->save();
    }

    // get single bompartconsumable api(int bomcategory_id)
    public function getBompartconsumableSingleApi($bompartconsumable_id)
    {
        $bompartconsumable = Bompartconsumable::findOrFail($bompartconsumable_id);
        return $bompartconsumable;
    }

    // bind custcategory to bompartconsumable
    public function bindBompartconsumableCustcat()
    {
        // die(var_dump(request('custcategory_id')['custcategory_id']));
        $bompartconsumable_id = request('bompartconsumable_id');
        $custcategory_id = request('custcategory_id')['custcategory_id'];
        $bompartconsumablecustcat = Bompartconsumablecustcat::where('bompartconsumable_id', $bompartconsumable_id)->where('custcategory_id', $custcategory_id)->first();

        if($bompartconsumablecustcat) {
            $bompartconsumablecustcat->delete();
        }else {
            if($custcategory_id) {
                Bompartconsumablecustcat::create([
                    'custcategory_id' => $custcategory_id,
                    'bompartconsumable_id' => $bompartconsumable_id,
                    'updated_by' => auth()->user()->id
                ]);
            }
        }
    }

    // retrieve all of the bom groups
    public function getBomgroupsApi()
    {
        $bomgroups = Bomgroup::with('updater');

        // reading whether search input is filled
        if(request('prefix') or request('name')){
            $bomgroups = $this->searchBomgroupFilter($bomgroups);
        }

        if(request('sortName')){
            $bomgroups = $bomgroups->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $bomgroups = $bomgroups->orderBy('prefix');
        }

        $pageNum = request('pageNum') ? request('pageNum') : 100;
        if($pageNum == 'All'){
            $bomgroups = $bomgroups->get();
        }else{
            $bomgroups = $bomgroups->paginate($pageNum);
        }

        $data = [
            'bomgroups' => $bomgroups
        ];

        return $data;
    }

    // get all of the bomgroups option()
    public function getBomgroupsSelectApi()
    {
        $bomgroups = Bomgroup::orderBy('prefix')->get();
        return $bomgroups;
    }

    // creating new bomgroup entry()
    public function createBomgroupApi()
    {
        $prefix = request('prefix');
        $name = request('name');
        $remark = request('remark');

        Bomgroup::create([
            'prefix' => $prefix,
            'name' => $name,
            'remark' => $remark,
            'updated_by' => auth()->user()->id
        ]);
    }

    // remove bomgroup entry(int bomgroup_id)
    public function destroyBomgroupApi($bomgroup_id)
    {
        $bomgroup = Bomgroup::findOrFail($bomgroup_id);
        $bomgroup->delete();
    }

    // update bom group api()
    public function updateBomgroupApi()
    {
        $id = request('id');
        $prefix = request('prefix');
        $name = request('name');
        $remark = request('remark');

        $bomgroup = Bomgroup::findOrFail($id);
        $bomgroup->prefix = $prefix;
        $bomgroup->name = $name;
        $bomgroup->remark = $remark;
        $bomgroup->updated_by = auth()->user()->id;
        $bomgroup->save();
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

    // pass value into filter search for parts DB (collection)
    private function searchBomtemplateDBFilter($bomtemplates)
    {
        $custcategory_id = request('custcategory_id');
        $bomtemplates = $bomtemplates->where('custcategories.id', '=', $custcategory_id);

        return $bomtemplates;
    }

    // pass value into filter search for bom vendings DB (collection)
    private function searchVendingDBFilter($bomvendings)
    {
        $people = request('formsearches');
        $bomvendings = $bomvendings->whereIn('people.id', $people);

        return $bomvendings;
    }

    // pass value into filter search for bommaintenance DB (collection) [query]
    private function searchBommaintenanceDBFilter($bommaintenances)
    {

        $person_id = request('person_id');
        $date_from = request('date_from');
        $date_to = request('date_to');
        $custcategory_id = request('custcategory_id');
        $technician_id = request('technician_id');
        $bomcomponent_id = request('bomcomponent_id');
        $issue_type = request('issue_type');

        if($person_id){
            $bommaintenances = $bommaintenances->where('people.id', $person_id);
        }
        if($date_from === $date_to){
            if($date_from != '' and $date_to != ''){
                $bommaintenances = $bommaintenances->whereDate('x.datetime', '=', $date_to);
            }
        }else{
            if($date_from){
                $bommaintenances = $bommaintenances->whereDate('x.datetime', '>=', $date_from);
            }
            if($date_to){
                $bommaintenances = $bommaintenances->whereDate('x.datetime', '<=', $date_to);
            }
        }
        if($custcategory_id){
            $bommaintenances = $bommaintenances->where('custcategories.id', $custcategory_id);
        }
        if($technician_id){
            $bommaintenances = $bommaintenances->where('technicians.id', $technician_id);
        }
        if($bomcomponent_id){
            $bommaintenances = $bommaintenances->where('bomcomponents.id', $bomcomponent_id);
        }
        if($issue_type){
            $bommaintenances = $bommaintenances->where('x.issue_type', $part_id);
        }
        return $bommaintenances;
    }

    // converting date and time into datetime(String date, String time)
    private function convertDateTimeCarbon($date, $time)
    {
        if(!$date) {
            $date = Carbon::today()->toDateString();
        }
        if(!$time) {
            $time = Carbon::now()->toTimeString();
        }
        $datetime = Carbon::parse($date.' '.$time);

        return $datetime;
    }

    // search filter for bomgroups(Query bomgroups)
    private function searchBomgroupFilter($bomgroups)
    {
        $prefix = request('prefix');
        $name = request('name');

        if($prefix){
            $bomgroups = $bomgroups->where('prefix', 'LIKE', '%'.$prefix.'%');
        }
        if($name){
            $bomgroups = $bomgroups->where('name', 'LIKE', '%'.$name.'%');
        }
        return $bomgroups;
    }
}
