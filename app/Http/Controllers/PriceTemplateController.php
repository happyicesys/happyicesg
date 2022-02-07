<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Person;
use App\PriceTemplate;
use App\PriceTemplateItem;
use Illuminate\Support\Facades\Redis;

class PriceTemplateController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get price template
    public function getPriceTemplateIndex()
    {
        return view('price-template.index');
    }

    // get index api
    public function getPriceTemplatesApi()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $query = PriceTemplate::with([
                                        'priceTemplateItems',
                                        'priceTemplateItems.item',
                                        'people'
                                    ]);

        if(request('name')) {
            $query = $query->where('name', 'LIKE', '%'.request('name').'%');
        }

        if(request('person_id')) {
            $people = request('person_id');
            if (count($people) == 1) {
                $people = [$people];
            }

            $query = $query->whereHas('people', function($query) use ($items) {
                $query->whereIn('id', $items);
            });
        }

        if(request('sortName')){
            $query = $query->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $query = $query->orderBy('name', 'asc')->get();
        }else{
            $query = $query->orderBy('name', 'asc')->paginate($pageNum);
        }

        return [
            'priceTemplates' => $query
        ];
    }

    // store new price template api(Request $request)
    public function storeUpdatePriceTemplateApi(Request $request)
    {
        $id = $request->id;
        $priceTemplateItems = $request->price_template_items;
        $name = $request->name;
        $remarks = $request->remarks;
        $currentUserId = auth()->user()->id;

        if($id) {
            $priceTemplate = PriceTemplate::findOrFail($id);
            $priceTemplate->name = $name;
            $priceTemplate->remarks = $remarks;
            $priceTemplate->save();
            $priceTemplate->priceTemplateItems()->delete();
        }else {
            $priceTemplate = PriceTemplate::create([
                'name' => $name,
                'remarks' => $remarks,
            ]);
        }
        if($priceTemplateItems) {
            foreach($priceTemplateItems as $item) {
                $this->syncPriceTemplateItem($item, $priceTemplate->id);
            }
        }
    }

    // destroy single
    public function deletePriceTemplateApi($id)
    {
        $model = PriceTemplate::findOrFail($id);

        if($model->priceTemplateItems) {
            foreach($model->priceTemplateItems as $priceTemplateItem) {
                $priceTemplateItem->delete();
            }
        }
        $model->delete();
    }

    // unbind single
    public function unbindPriceTemplatePerson($id)
    {
        $model = Person::findOrFail($id);
        $model->price_template_id = null;
        $model->save();
    }

    // add new single
    public function createPriceTemplateApi(Request $request)
    {
        $name = $request->name;

        if($name) {
            PriceTemplate::create([
                'name' => $name
            ]);
        }
    }

    // bind price template with person
    public function bindPriceTemplatePersonApi(Request $request)
    {
        $price_template_id = $request->price_template_id;
        $person_id = $request->person_id;

        if($price_template_id and $person_id) {
            $model = Person::findOrFail($person_id);
            $model->price_template_id = $price_template_id;
            $model->save();
        }
    }

    // sync new route template items
    private function syncPriceTemplateItem($priceTemplateItem, $id)
    {

        $itemId = $priceTemplateItem['item']['id'];
        $priceTemplateId = $id;
        $sequence = $priceTemplateItem['sequence'];
        $retailPrice = $priceTemplateItem['retail_price'];
        $quotePrice = $priceTemplateItem['quote_price'];

        PriceTemplateItem::create([
            'item_id' => $itemId,
            'price_template_id' => $priceTemplateId,
            'sequence' => $sequence,
            'retail_price' => $retailPrice,
            'quote_price' => $quotePrice,
        ]);
    }

}
