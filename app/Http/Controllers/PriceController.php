<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Person;
use App\Price;
use App\Fprice;
use App\Item;
use Laracasts\Flash\Flash;
use DB;

class PriceController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // 10-Feb now fully depends only on store
    public function store(PriceRequest $request)
    {

        $person_id = $request->input('person_id');
        $retail_price = $request->retail;
        $quote_price = $request->quote;

        foreach ($quote_price as $index => $quote) {
            if ($quote != null) {
                if (auth()->user()->hasRole('franchisee')) {
                    $price = Fprice::wherePersonId($person_id)->whereItemId($index)->first();
                } else {
                    $price = Price::wherePersonId($person_id)->whereItemId($index)->first();
                }

                if ($price) {
                    $price->retail_price = $retail_price[$index];
                    $price->quote_price = $quote_price[$index];
                    $price->save();
                } else {
                    if (auth()->user()->hasRole('franchisee')) {
                        $price = new Fprice();
                    } else {
                        $price = new Price();
                    }
                    $price->retail_price = $retail_price[$index];
                    $price->quote_price = $quote_price[$index];
                    $price->person_id = $person_id;
                    $price->item_id = $index;
                    $price->save();
                }
            } else {
                if (auth()->user()->hasRole('franchisee')) {
                    $price = Fprice::wherePersonId($person_id)->whereItemId($index)->first();
                } else {
                    $price = Price::wherePersonId($person_id)->whereItemId($index)->first();
                }

                // if ($retail_price[$index] == 0 or $retail_price[$index] == null) {
                    if ($price) {
                        $price->delete();
                    }
/*
                } else {
                    $price->retail_price = $retail_price[$index];
                    $price->quote_price = $quote_price[$index];
                    $price->person_id = $person_id;
                    $price->item_id = $index;
                    $price->save();
                } */
            }
        }
/*
        $retail_price = $request->input('retail_price');

        if(! $request->has('quote_price')){
            $request->merge(array('quote_price' => $this->calquote($person_id, $retail_price)));
        }

        $input = $request->all();
        $price = Price::create($input);
         */

        return Redirect::action('PersonController@edit', $person_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $person = Person::findOrFail($id);

        return view('person.price.create', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $price = Price::findOrFail($id);

        return view('person.price.edit', compact('price'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PriceRequest $request, $id)
    {
        $price = Price::findOrFail($id);
        $retail_price = $request->input('retail_price');

        if (!$request->has('quote_price')) {
            $request->merge(array('quote_price' => $this->calquote($price->person_id, $retail_price)));
        }

        $input = $request->all();
        $price->update($input);

        return Redirect::action('PersonController@edit', $price->person_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $person_id = $price->person_id;
        $price->delete();

        return Redirect::action('PersonController@edit', $person_id);
    }

    // return prices for specific item and person (int $item_id, int $person_id)
    public function lookupPrices($item_id, $person_id)
    {
        $prices = Price::where('item_id', $item_id)->where('person_id', $person_id)->get();

        return $prices;
    }

    /*
    // return items horizontal th for price matrix
    public function getPriceMatrixItems()
    {
        $items = $this->filterPriceMatrixItems();

        return $items;
    }

    // return items horizontal th for price matrix
    public function getPriceMatrixPeople()
    {
        $people = $this->filterPriceMatrixPeople();

        return $people;
    }*/

    // return items horizontal th for price matrix index
    public function getPriceMatrixIndex()
    {
        return view('person.price_matrix');
    }


    // return items horizontal th for price matrix
    public function getPriceMatrixIndexApi()
    {
        $people = DB::table('people')
                    ->select(
                        'people.id', 'people.cust_id', 'people.custcategory_id', 'people.cost_rate'
                    );

        $people = $this->filterPriceMatrixPeople();

        $people = $people->orderBy('cust_id')->get();

        $items = DB::table('items')
                    ->select(
                        'items.id', 'items.product_id', 'items.name', 'items.itemcategory_id'
                    );

        $items = $this->filterPriceMatrixItems();

        $items = $items->orderBy('product_id')->get();

        $prices = array();

        foreach($people as $index1 => $person) {
            foreach($items as $index2 => $item) {
                $price = DB::table('prices')
                            ->where('person_id', $person->id)
                            ->where('item_id', $item->id)
                            ->first();

                $prices[$index1][$index2] = [
                    'person_id' => $person->id,
                    'price_id' => $price ? $price->id : '',
                    'item_id' => $item->id,
                    'retail_price' => $price ? $price->retail_price : '',
                    'quote_price' => $price ? $price->quote_price : ''
                ];
            }
        }

        $data = [
            'people' => $people,
            'items' => $items,
            'prices' => $prices
        ];

        return $data;
    }

    // edit and update the price matrix
    public function editPriceMatrixApi()
    {
        $person_id = request('person_id');
        $item_id = request('item_id');
        $retail_price = request('retail_price');
        $quote_price = request('quote_price');
        $price_id = request('price_id');

        $price = Price::find($price_id);

        if($price) {
            if($retail_price and $quote_price) {
                $price->update([
                    'retail_price' => $retail_price,
                    'quote_price' => $quote_price
                ]);
            }else {
                $price->delete();
            }

        }else {
            Price::create([
                'person_id' => $person_id,
                'item_id' => $item_id,
                'retail_price' => $retail_price,
                'quote_price' => $quote_price,
            ]);
        }
    }

    // override retail price and quote price by checkbox
    public function overridePriceMatrixApi()
    {
        $peopleArr = request('people');
        $retailPrice = request('retailPrice');
        $quotePrice = request('quotePrice');
        $itemId = request('itemId');

        $item = Item::findOrFail($itemId);
        if($peopleArr) {
            foreach($peopleArr as $person) {
                if(isset($person['check'])) {
                    if($person['check']) {
                        $price = Price::where('person_id', $person['id'])->where('item_id', $item->id)->first();
                        if($quotePrice != null and $quotePrice != '') {
                            if($price) {
                                $price->retail_price = $retailPrice;
                                $price->quote_price = $quotePrice;
                                $price->save();
                            }else {
                                $newPrice = new Price();
                                $newPrice->retail_price = $retailPrice;
                                $newPrice->quote_price = $quotePrice;
                                $newPrice->item_id = $item->id;
                                $newPrice->person_id = $person['id'];
                                $newPrice->save();
                            }
                        }else {
                            if($price) {
                                $price->delete();
                            }
                        }
                    }
                }
            }
        }
    }


    // update the person costrate
    public function editCostrateApi()
    {
        $person_id = request('id');
        $cost_rate = request('cost_rate');
        $person = Person::find($person_id);

        if($person) {
            $person->update([
                'cost_rate' => $cost_rate
            ]);
        }
    }

    // processing batch confirm for price matrix()
    public function batchConfirmPriceMatrix()
    {
        $checkboxes = request('checkbox');
        $costrates = request('cost_rate');
        $retailprices = request('retail_price');
        $quoteprices = request('quote_price');

        if ($checkboxes) {
            foreach ($checkboxes as $index => $checkbox) {
                $person = Person::findOrFail($index);
                $person->cost_rate = $costrates[$index];
                $person->save();

                foreach ($retailprices as $retailindex => $retailprice) {
                    if (explode('-', $retailindex)[1] == $index) {
                        $price = Price::where('person_id', $index)->where('item_id', explode('-', $retailindex)[0])->first();
                        if ($price) {
                            if (($retailprice != 0.00 and $retailprice != '') or ($quoteprices[$retailindex] != 0.00 and $quoteprices[$retailindex] != '')) {
                                $price->retail_price = $retailprice;
                                $price->quote_price = $quoteprices[$retailindex];
                                $price->save();
                            } else {
                                $price->delete();
                            }
                        } else {
                            if (($retailprice != 0.00 and $retailprice != '') or ($quoteprices[$retailindex] != 0.00 and $quoteprices[$retailindex] != '')) {
                                $price = new Price();
                                $price->person_id = $person->id;
                                $price->item_id = explode('-', $retailindex)[0];
                                $price->retail_price = $retailprice;
                                $price->quote_price = $quoteprices[$retailindex];
                                $price->save();
                            }
                        }
                        // dd($price->quote_price, $retailprice, $quoteprices[$retailindex]);

                    }
                }
            }

        } else {
            Flash::error('Please select at least one checkbox');
        }
        return redirect()->action('PriceController@getPriceMatrix');
    }

    // return price matrix items filter api()
    private function filterPriceMatrixItems()
    {
        $product_id = request('product_id');
        $name = request('name');
        $is_inventory = request('is_inventory');

        $items = new Item();

        if($product_id) {
            $items = $items->where('items.product_id', 'LIKE', '%' . $product_id . '%');
        }
        if($name) {
            $items = $items->where('items.name', 'LIKE', '%' . $name . '%');
        }

        if($is_inventory) {
            $items = $items->where('items.is_inventory', $is_inventory);
        }

        return $items;
    }

    // return price matrix customers filter api()
    private function filterPriceMatrixPeople()
    {
        $cust_id = request('cust_id');
        $strictCustId = request('strictCustId');
        $custcategory_id = request('custcategory_id');
        $company = request('company');
        $active = request('active');

        $people = new Person();

        if ($cust_id) {
            if($strictCustId) {
                $people = $people->where('people.cust_id', 'LIKE', $cust_id . '%');
            }else {
                $people = $people->where('people.cust_id', 'LIKE', '%'. $cust_id . '%');
            }
        }
        if ($custcategory_id) {
            $people = $people->where('people.custcategory_id', $custcategory_id);
        }
        if ($company) {
            $people = $people->where('people.company', 'LIKE', '%' . $company . '%');
        }
        if($active) {
            $actives = $active;
            if (count($actives) == 1) {
                $actives = [$actives];
            }
            $people = $people->whereIn('people.active', $actives);
        }


        return $people;
    }

    private function calquote($id, $retail_price)
    {
        $person = Person::findOrFail($id);

        $cost_rate = $person->cost_rate;

        if ($cost_rate) {
            $result = round($retail_price * ($cost_rate / 100), 2);
            return $result;
        } else {
            return false;
        }

    }
}