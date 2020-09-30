<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Operationdate;
use App\Person;
use App\RouteTemplate;
use App\RouteTemplateItem;
use App\Transaction;
use Illuminate\Support\Facades\Redis;

// use App\HasProfileAccess;

class RouteTemplateController extends Controller
{
    // use HasProfileAccess;

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get scheduled routes person
    public function getRouteTemplateIndex()
    {
        return view('route-template.index');
    }

    // retrieve api data list for the route template index (Request $request)
    public function getRouteTemplatesApi(Request $request)
    {
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $routeTemplates = RouteTemplate::with(['routeTemplateItems', 'routeTemplateItems.person', 'routeTemplateItems.person.custcategory', 'routeTemplateItems.person.zone']);

        // reading whether search input is filled
        $routeTemplates = $this->searchRouteTemplateFilter($routeTemplates, $request);

        if ($request->sortName) {
            $routeTemplates = $routeTemplates->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        // add user profile filters
        // $routeTemplates = $this->filterUserDbProfile($routeTemplates);
        if ($pageNum == 'All') {
            $routeTemplates = $routeTemplates->orderBy('created_at', 'desc')->get();
        } else {
            $routeTemplates = $routeTemplates->orderBy('created_at', 'desc')->paginate($pageNum);
        }

        $data = [
            'routeTemplates' => $routeTemplates,
        ];

        return $data;
    }

    // store new route template api(Request $request)
    public function storeUpdateRouteTemplateApi(Request $request)
    {
        $id = $request->id;
        $routeTemplateItems = $request->route_template_items;
        $name = $request->name;
        $desc = $request->desc;
        $currentUserId = auth()->user()->id;

        if($id) {
            $routeTemplate = RouteTemplate::findOrFail($id);
            $routeTemplate->name = $name;
            $routeTemplate->desc = $desc;
            $routeTemplate->updated_by = $currentUserId;
            $routeTemplate->save();
            $routeTemplate->routeTemplateItems()->delete();
        }else {
            $routeTemplate = RouteTemplate::create([
                'name' => $name,
                'desc' => $desc,
                'created_by' => $currentUserId
            ]);
        }


        if($routeTemplateItems) {
            foreach($routeTemplateItems as $item) {
                $this->syncRouteTemplateItem($item, $routeTemplate->id);
            }
        }
    }

    // generate invoices based on template
    public function generateTemplateInvoiceApi(Request $request)
    {
        // dd($request->all());
        $invoiceDate = $request->invoiceDate;
        $driver = $request->driver ? $request->driver : null;
        $routeTemplates = $request->alldata;

        if($routeTemplates) {
            foreach($routeTemplates as $routeTemplate) {
                if(isset($routeTemplate['check'])) {
                    $routeTemplate = RouteTemplate::findOrFail($routeTemplate['id']);
                    foreach($routeTemplate->routeTemplateItems as $item) {
                        $person = Person::findOrFail($item->person_id);
                        Transaction::create([
                            'delivery_date' => $invoiceDate,
                            'person_id' => $person->id,
                            'sequence' => $item->sequence,
                            'driver' => $driver,
                            'status' => 'Pending',
                            'pay_status' => 'Owe',
                            'updated_by' => auth()->user()->name,
                            'created_by' => auth()->user()->id,
                            'del_postcode' => $person->del_postcode,
                            'del_address' => $person->del_address,
                            'del_lat' => $person->del_lat,
                            'del_lng' => $person->del_lng
                        ]);

                        $prevOpsDate = Operationdate::where('person_id', $person->id)->whereDate('delivery_date', '=', $invoiceDate)->first();

                        if($prevOpsDate) {
                            $prevOpsDate->color = 'Orange';
                            $prevOpsDate->save();
                        }else {
                            $opsdate = new Operationdate;
                            $opsdate->person_id = $person->id;
                            $opsdate->delivery_date = $invoiceDate;
                            $opsdate->color = 'Orange';
                            $opsdate->save();
                        }
                    }
                }
            }
        }
    }

    // generate route template from job assign page
    public function createRouteTemplateFromJobassignApi(Request $request)
    {
        $drivers = $request->drivers;
        $templateName = $request->templateName;
        $templateDesc = $request->templateDesc;

        $routeTemplate = RouteTemplate::create([
            'name' => $templateName,
            'desc' => $templateDesc,
            'created_by' => auth()->user()->id
        ]);

        if($drivers) {
            foreach($drivers as $driverindex => $driver) {
                foreach($driver['transactions'] as $transactionindex => $transaction) {
                    if(isset($transaction['check'])) {
                        if($transaction['check']) {
                            $itemArr = [];
                            $itemArr['person']['id'] = $transaction['person_id'];
                            $itemArr['sequence'] = $transaction['sequence'];
                            $this->syncRouteTemplateItem($itemArr, $routeTemplate->id);
                        }
                    }
                    unset($drivers[$driverindex]['transactions'][$transactionindex]);
                }
            }
        }
    }

    // delete route template with its children
    public function deleteRouteTemplateApi($id)
    {
        $routeTemplate = RouteTemplate::findOrFail($id);
        $routeTemplate->routeTemplateItems()->delete();
        $routeTemplate->delete();
    }

    // sync new route template items
    private function syncRouteTemplateItem($routeTemplateItem, $id)
    {

        $personId = $routeTemplateItem['person']['id'];
        $routeTemplateId = $id;
        $sequence = $routeTemplateItem['sequence'];

        RouteTemplateItem::create([
            'person_id' => $personId,
            'route_template_id' => $routeTemplateId,
            'sequence' => $sequence
        ]);
    }

    // conditional filter parser(Collection $query, Formrequest $request)
    private function searchRouteTemplateFilter($query, $request)
    {
        $custId = $request->cust_id;
        $strictCustId = $request->strictCustId;
        $custCategory = $request->custcategory;
        $company = $request->company;
        $tags = $request->tags;
        $profileId = $request->profile_id;
        $zoneId = $request->zone_id;

        if($custId) {
            if($strictCustId) {
                $query = $query->whereHas('routeTemplateItems', function($query) use ($custId) {
                    $query->whereHas('person', function($query) use ($custId) {
                        $query->where('cust_id', 'LIKE', '%'.$custId.'%');
                    });
                });
            }else {
                $query = $query->whereHas('routeTemplateItems', function($query) use ($custId) {
                    $query->whereHas('person', function($query) use ($custId) {
                        $query->where('cust_id', 'LIKE', '%'. $custId . '%');
                    });
                });
            }
        }

        if($custCategory) {
            if (count($custCategory) == 1) {
                $custCategory = [$custCategory];
            }
            $query = $query->whereHas('routeTemplateItems', function($query) use ($custCategory) {
                $query->whereHas('person', function($query) use ($custCategory) {
                    $query->whereIn('custcategory_id', $custcategory);
                });
            });
        }

        if($company) {
            $query = $query->whereHas('routeTemplateItems', function($query) use ($company) {
                $query->whereHas('person', function($query) use ($company) {
                    $query->where('company', 'LIKE', '%'. $company . '%');
                });
            });
        }

        if($tags) {
            if (count($tags) == 1) {
                $tags = [$tags];
            }
            $query = $query->whereHas('routeTemplateItems', function($query) use ($tags) {
                $query->whereHas('person', function($query) use ($tags) {
                    $query->whereHas('persontagattaches', function($query) use ($tags) {
                        $query->whereIn('id', $tags);
                    });
                });
            });
        }

        if($profileId) {
            $query = $query->whereHas('routeTemplateItems', function($query) use ($profileId) {
                $query->whereHas('person', function($query) use ($profileId) {
                    $query->where('profile_id', $profileId);
                });
            });
        }

        if($zoneId) {
            $query = $query->whereHas('routeTemplateItems', function($query) use ($zoneId) {
                $query->whereHas('person', function($query) use ($zoneId) {
                    $query->where('zone_id', $zoneId);
                });
            });
        }

        return $query;
    }

}
