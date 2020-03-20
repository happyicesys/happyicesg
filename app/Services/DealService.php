<?php

namespace App\Services;
use App\Deal;
use DB;

class DealService
{
    public function getDeals($request)
    {
        $query = Deal::leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
        ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
        ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
        ->leftJoin('deliveryorders', 'deliveryorders.transaction_id', '=', 'transactions.id')
        ->whereHas('transaction', function($query) use ($request) {
            if($request->transaction_id) {
                $query->id($request->transaction_id);
            }
            if($request->statuses) {
                $query->fullStatus($request->statuses);
            }
            if($request->pay_status) {
                $query->payStatus($request->pay_status);
            }
            if($request->updated_by) {
                $query->updatedBy($request->updated_by);
            }
            if($request->updated_at) {
                $query->updatedAt($request->updated_at);
            }
            if($request->driver) {
                $query->driver($request->driver);
            }
            if($request->updated_by) {
                $query->updatedBy($request->updated_by);
            }
            if($request->po_no) {
                $query->poNo($request->po_no);
            }
            if($request->contact) {
                $query->contact($request->contact);
            }
            if($request->gst) {
                $query->isGst($request->gst);
            }
            if($request->is_gst_inclusive) {
                $query->isGstInclusive($request->is_gst_inclusive);
            }
            if($request->gst_rate) {
                $query->gstRate($request->gst_rate);
            }
            // dd($request->delivery_from, request('delivery_from'))
            if($request->delivery_from) {
                $query->deliveryDateFrom($request->delivery_from);
            }
            if($request->delivery_to) {
                $query->deliveryDateTo($request->delivery_to);
            }

            $query->whereHas('person', function($query) use ($request) {
                if($request->cust_id) {
                    $query->custId($request->cust_id);
                }
                if($request->company) {
                    $query->company($request->company);
                }
                if($request->area_groups) {
                    $query->areaGroups($request->area_groups);
                }
                if($request->person_active) {
                    $query->active($request->person_active);
                }

                $query->whereHas('custcategory', function($query) use ($request) {
                    if($request->custcategory) {
                        if($request->exclude_custcategory) {
                            $query->excludeCustId($request->custcategory);
                        }else {
                            $query->custId($request->custcategory);
                        }
                    }
                });

                if($request->franchisee_id) {
                    $query->filterFranchiseePeople();
                }

                $query->whereHas('profile', function($query) use ($request) {
                    if($request->profile_id) {
                        $query->id($request->profile_id);
                    }
                });
            });
        })->whereHas('item', function($query) use ($request) {
            if($request->is_inventory) {
                $query->isInventory($request->is_inventory);
            }
            if($request->is_commission) {
                $query->isCommission($request->is_commission);
            }
            if($request->product_id) {
                $query->productId($request->product_id);
            }
            if($request->product_name) {
                $query->name($request->product_name);
            }
        });


        return $query;
    }

    // return transactions from deals query
    public function getTransactions($request, $pagination = false, $tax = true)
    {

        $query = $this->getDeals($request);

        if($tax) {
            $total = $this->calculateAmountTotalWithTax(clone $query);
        }else {
            $total = $this->calculateAmountWithoutTotalWithTax(clone $query);
        }

        $query = $query->groupBy('transactions.id')
        ->select(
            'people.cust_id', 'people.company',
            'people.name', 'people.id as person_id', 'transactions.del_postcode',
            'transactions.status', 'transactions.delivery_date', 'transactions.driver',
            'transactions.total_qty', 'transactions.pay_status', 'transactions.is_deliveryorder',
            'transactions.updated_by', 'transactions.updated_at', 'transactions.delivery_fee', 'transactions.id',
            'transactions.po_no', 'transactions.name', 'transactions.contact', 'transactions.del_address',
            DB::raw('DATE(transactions.delivery_date) AS del_date'),
            DB::raw('ROUND((CASE WHEN transactions.gst=1 THEN (
                        CASE
                        WHEN transactions.is_gst_inclusive=0
                        THEN total*((100+transactions.gst_rate)/100)
                        ELSE transactions.total
                        END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2) AS total'),
            'profiles.id as profile_id', 'transactions.gst', 'transactions.is_gst_inclusive', 'transactions.gst_rate',
            'custcategories.name as custcategory',
            DB::raw('DATE(deliveryorders.delivery_date1) AS delivery_date1'),
            'deliveryorders.po_no AS do_po', 'deliveryorders.requester_name', 'deliveryorders.pickup_location_name',
            'deliveryorders.delivery_location_name',
            DB::raw('SUBSTRING(people.area_group, 1, 1) AS west'),
            DB::raw('SUBSTRING(people.area_group, 3, 1) AS east'),
            DB::raw('SUBSTRING(people.area_group, 5, 1) AS others')
        );

        if($request->sortname) {
            $query = $query->orderBy($request->sortname, $request->sortBy);
        }else {
            $query = $query->orderBy('transactions.created_at', 'desc');
        }
        if($pagination) {
            $query = $query->paginate($pagination);
        }else {
            $query = $query->get();
        }

        return [
            'transactions' => $query,
            'total' => $total
        ];
    }

    // looping and calculate totals
    private function calculateAmountTotalWithTax($query)
    {
        $total = 0;

        $total = $query->sum(DB::raw('ROUND((
            CASE WHEN transactions.gst=1
            THEN
                (CASE WHEN transactions.is_gst_inclusive=1
                THEN deals.amount
                ELSE deals.amount * (100 + transactions.gst_rate)/ 100
                END)
            ELSE
                deals.amount
            END)
        , 2)'));

        return $total;
    }

    // looping and calculate totals
    private function calculateAmountWithoutTotalWithTax($query)
    {
        $total = 0;

        $total = $query->sum(DB::raw('ROUND((
            CASE WHEN transactions.gst=1
            THEN
                (CASE WHEN transactions.is_gst_inclusive=1
                THEN deals.amount / (100 + transactions.gst_rate)/ 100
                ELSE deals.amount
                END)
            ELSE
                deals.amount
            END)
        , 2)'));

        return $total;
    }
}