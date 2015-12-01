<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $fillable=[
        'total', 'delivery_date', 'status', 
        'person_id', 'user_id', 'transremark'
    ];

    public function setDeliveryDateAttribute($date)
    {
        $this->attributes['delivery_date'] = Carbon::parse($date);
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

    public function sale()
    {
        return $this->hasOne('App\Sale');
    }

    public function deals()
    {
        return $this->hasMany('App\Deal');
    }    

    public function getCreatedAttribute($date)
    {
        return Carbon::parse($date)->format('d-F-Y');
    }

    public function getDeliveryDateAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('d-F-Y');    

        }else{

            return null;
        }
        
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y');
    }  

    //select field populate selected
    /*public function getPersonIdAttribute()
    {
        return $this->person->lists('id')->all();
    } */      

    /**
     * search and retrieve month data
     * @param $month in integer
     * @return mixed
     */
    public function scopeSearchDateRange($query, $datefrom, $dateto)
    {
        $datefrom = Carbon::createFromFormat('d-F-Y', $datefrom);

        $dateto = Carbon::createFromFormat('d-F-Y', $dateto);

        return $query->whereBetween('created_at',array($datefrom, $dateto));
    }

    public function scopeSearchYearRange($query, $period)
    {
       if($period == 'this'){

           return $query->whereBetween('created_at', array(Carbon::now()->startOfYear(), Carbon::now()->endOfYear()));

       }else if($period == 'last'){

           return $query->whereBetween('created_at', array(Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()));

       }
    }

    public function scopeSearchMonthRange($query, $month)
    {
        if($month != '0'){

            return $query->whereBetween('created_at', array(Carbon::create(Carbon::now()->year, $month)->startOfMonth(), Carbon::create(Carbon::now()->year, $month)->endOfMonth()));

        }else{

            return $query->whereBetween('created_at', array(Carbon::now()->startOfYear(), Carbon::now()->endOfYear()));

        }
    }    
}
