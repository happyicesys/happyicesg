<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DtdTransaction extends Model
{
    protected $table = 'dtdtransactions';

    protected $fillable=[
        'total','total_qty', 'transremark',
        'person_code', 'name', 'person_id',
        'delivery_date', 'order_date', 'driver',
        'status', 'pay_status', 'del_address',
        'po_no', 'cancel_trace','user_id',
        'pay_method', 'note', 'paid_at',
        'updated_by', 'paid_by', 'transaction_id',
        'contact', 'del_postcode'
    ];

    protected $dates =[
        'delivery_date', 'order_date', 'paid_at'
    ];

    public function setDeliveryDateAttribute($date)
    {
        if($date){

            $this->attributes['delivery_date'] = Carbon::parse($date);

        }else{

            $this->attributes['delivery_date'] = null;

        }
    }

    public function setOrderDateAttribute($date)
    {
        if($date){

            $this->attributes['order_date'] = Carbon::parse($date);

        }else{

            $this->attributes['order_date'] = null;

        }
    }

    public function setTransremarkAttribute($value)
    {
        $this->attributes['transremark'] = $value ?: null;
    }

    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function dtddeals()
    {
        return $this->hasMany('App\DtdDeal');
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d M y');
    }
/*
    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d M y h:i A');
    }  */

    public function getDeliveryDateAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('Y-m-d');

        }else{

            return null;
        }

    }

    public function getOrderDateAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('Y-m-d');

        }else{

            return null;
        }

    }

    /**
     * search and retrieve month data
     * @param $month in integer
     * @return mixed
     */
    public function scopeSearchDateRange($query, $datefrom, $dateto)
    {
        $datefrom = Carbon::createFromFormat('d M y', $datefrom)->format('Y-m-d');
        // dd($datefrom);

        $dateto = Carbon::createFromFormat('d M y', $dateto)->format('Y-m-d');

        // return $query->whereBetween('delivery_date',array($datefrom, $dateto));
        return $query->where('delivery_date', '>=', $datefrom)->where('delivery_date', '<=', $dateto);
    }

    public function scopeSearchYearRange($query, $period)
    {
       if($period == 'this'){

           // return $query->whereBetween('delivery_date', array(Carbon::now()->startOfYear(), Carbon::now()->endOfYear()));
        return $query->where('delivery_date', '>=', Carbon::now()->startOfYear()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::now()->endOfYear()->format('Y-m-d'));

       }else if($period == 'last'){

           // return $query->whereBetween('delivery_date', array(Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()));
        return $query->where('delivery_date', '>=', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));

       }
    }

    public function scopeSearchMonthRange($query, $month)
    {
        if($month != '0'){

            // return $query->whereBetween('delivery_date', array(Carbon::create(Carbon::now()->year, $month)->startOfMonth(), Carbon::create(Carbon::now()->year, $month)->endOfMonth()));
            return $query->where('delivery_date', '>=', Carbon::create(Carbon::now()->year, $month)->startOfMonth()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::create(Carbon::now()->year, $month)->endOfMonth()->format('Y-m-d'));

        }else{

            // return $query->whereBetween('delivery_date', array(Carbon::now()->startOfYear(), Carbon::now()->endOfYear()));
            return $query->where('delivery_date', '>=', Carbon::now()->startOfYear()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::now()->endOfYear()->format('Y-m-d'));

        }
    }
}
