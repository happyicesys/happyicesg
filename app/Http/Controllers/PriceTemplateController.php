<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Attachment;
use App\Person;
use App\PriceTemplate;
use App\PriceTemplateItem;
use App\PriceTemplateItemUom;
use Carbon\Carbon;
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
    public function getPriceTemplatesApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $query = PriceTemplate::with([
                                        'priceTemplateItems' => function($query) {
                                            $query->orderBy('sequence');
                                        },
                                        'attachments',
                                        'priceTemplateItems.item',
                                        'priceTemplateItems.item.itemUoms',
                                        'priceTemplateItems.priceTemplateItemUoms',
                                        'people'  => function($query) use ($request){
                                            if($request->person_id) {
                                                $query->whereIn('id', $request->person_id);
                                            }
                                            if($request->active) {
                                                $query->whereIn('active', $request->active);
                                            }
                                            $query->orderBy('cust_id');
                                        }
                                    ]);

        if(request('name')) {
            $query = $query->where('name', 'LIKE', '%'.request('name').'%');
        }

        if(request('person_id')) {
            $people = request('person_id');

            $query = $query->whereHas('people', function($query) use ($people) {
                $query->whereIn('id', $people);
            });
        }

        if(request('active')) {
            $active = request('active');

            $query = $query->where(function($query) use ($active){
                $query->whereDoesntHave('people');
                $query->orWhereHas('people', function($query) use ($active) {
                    $query->whereIn('active', $active);
                });

            });
        }

        if(request('sortName')){
            $query = $query->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $query = $query->orderBy('created_at', 'desc')->get();
        }else{
            $query = $query->orderBy('created_at', 'desc')->paginate($pageNum);
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
            $debugArr = [];
            if($priceTemplate->priceTemplateItems()->exists()) {
                foreach($priceTemplate->priceTemplateItems as $priceTemplateItem) {
                    if($priceTemplateItem->priceTemplateItemUoms()->exists()) {
                        foreach($priceTemplateItem->priceTemplateItemUoms as $priceTemplateItemUom) {
                            $priceTemplateItemUom->delete();
                        }
                    }
                    $priceTemplateItem->delete();
                }
            }

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

    public function uploadAttachment(Request $request)
    {
        if($file = request()->file('file')){
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $url = 'price-template/'.$priceTemplate->id.'/'.$name;
            $urlStore = Storage::put($url, file_get_contents($file->getRealPath()), 'public');
            $fullUrl = Storage::url($urlStore);
            $priceTemplate->attachments()->create([
                'url' => $url,
                'fullUrl' => $fullUrl,
            ]);
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

    public function replicatePriceTemplateApi(Request $request)
    {
        $model = PriceTemplate::findOrFail($request->id);

        $replicatedModel = $model->replicate();
        $replicatedModel->name = $replicatedModel->name.'-replicated';
        $replicatedModel->save();

        if($model->priceTemplateItems()->exists()) {
            foreach($model->priceTemplateItems as $priceTemplateItem) {
                $replicatedPriceTemplateItem = $priceTemplateItem->replicate();
                $replicatedPriceTemplateItem->price_template_id = $replicatedModel->id;
                $replicatedPriceTemplateItem->save();
            }
        }
    }

    public function sortSequenceApi(Request $request)
    {
        $form = $request->form;

        if($form) {
            $keys = array_column($form['price_template_items'], 'sequence');
            array_multisort($keys, SORT_ASC, $form['price_template_items']);
        }

        return $form;
    }

    public function renumberSequenceApi(Request $request)
    {
        $form = request('form');

        if($form) {
            $assignindex = 1;
            foreach($form['price_template_items'] as $index => $item) {
                $priceTemplateItem = PriceTemplateItem::findOrFail($item['id']);
                $priceTemplateItem->sequence = $assignindex;
                $priceTemplateItem->save();
                $form['price_template_items'][$index]['sequence'] = $assignindex;
                // dd($transaction, $transaction['sequence']);
                $assignindex ++;
            }
        }
        return $form;
    }

    // upload attachment file()
    public function storeAttachmentApi(Request $request, $priceTemplateId)
    {
        $priceTemplate = PriceTemplate::findOrFail($priceTemplateId);
        // $priceTemplate->updated_by = auth()->user()->id;
        $priceTemplate->updated_at = Carbon::now();
        $priceTemplate->save();

        if($image = request()->file('image_file')){
            $name = (Carbon::now()->format('dmYHi')).$image->getClientOriginalName();
            $image->move('price_template/'.$priceTemplate->id.'/', $name);
            $priceTemplate->attachments()->create([
                'url' => '/price_template/'.$priceTemplate->id.'/'.$name,
                'full_url' => '/price_template/'.$priceTemplate->id.'/'.$name,
            ]);
        }
    }

    public function deleteAttachmentApi()
    {
        $attachmentId = request('attachmentId');

        $attachment = Attachment::findOrFail($attachmentId);
        $attachment->delete();
    }

    public function togglePriceTemplateItemUomApi($priceTemplateItemId, $itemUomId)
    {
        $priceTemplateItem = PriceTemplateItem::findOrFail($priceTemplateItemId);

        // dd($priceTemplateItem->toArray());
        if($priceTemplateItem->priceTemplateItemUoms()->exists()) {
            $priceTemplateItemUomObj = $priceTemplateItem->priceTemplateItemUoms()->whereHas('itemUom', function($query) use ($itemUomId) {
                $query->where('id', $itemUomId);
            })->first();

            if($priceTemplateItemUomObj) {
                $priceTemplateItemUomObj->delete();
            }else {
                PriceTemplateItemUom::create([
                    'price_template_item_id' => $priceTemplateItemId,
                    'item_uom_id' => $itemUomId,
                ]);
            }
        }else {
            PriceTemplateItemUom::create([
                'price_template_item_id' => $priceTemplateItemId,
                'item_uom_id' => $itemUomId,
            ]);
        }
    }

    // sync new route template items
    private function syncPriceTemplateItem($prevPriceTemplateItem, $id)
    {
        $prevPriceTemplateId =  $prevPriceTemplateItem['id'];
        $itemId = $prevPriceTemplateItem['item']['id'];
        $priceTemplateId = $id;
        $sequence = $prevPriceTemplateItem['sequence'];
        $retailPrice = $prevPriceTemplateItem['retail_price'];
        $quotePrice = $prevPriceTemplateItem['quote_price'];
        $prevPriceTemplateItemUoms = $prevPriceTemplateItem['price_template_item_uoms'];

        $priceTemplateItem = PriceTemplateItem::create([
            'item_id' => $itemId,
            'price_template_id' => $priceTemplateId,
            'sequence' => $sequence,
            'retail_price' => $retailPrice,
            'quote_price' => $quotePrice,
        ]);

        if(!$prevPriceTemplateId) {
            if($itemUoms = $priceTemplateItem->item->itemUoms) {
                foreach($itemUoms as $itemUom) {
                    PriceTemplateItemUom::create([
                        'price_template_item_id' => $priceTemplateItem->id,
                        'item_uom_id' => $itemUom->id,
                    ]);
                }
            }
        }

        if($prevPriceTemplateItemUoms) {
            foreach($prevPriceTemplateItemUoms as $prevPriceTemplateItemUom) {
                PriceTemplateItemUom::create([
                    'price_template_item_id' => $priceTemplateItem->id,
                    'item_uom_id' => $prevPriceTemplateItemUom['item_uom_id'],
                ]);

                // PriceTemplateItemUom::findOrFail($prevPriceTemplateItemUom['id'])->delete();
            }
        }
    }

}
