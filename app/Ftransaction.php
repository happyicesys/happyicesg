<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ftransaction extends Model
{
    use HasProfileAccess;

    protected $fillable=[
        'ftransaction_id', 'total', 'collection_datetime', 'person_id', 'digital_clock',
        'analog_clock', 'sales', 'franchisee_id', 'taxtotal',
        'finaltotal'
    ];

    protected $dates =[
        'collection_datetime'
    ];

    public function setDigitalClockAttribute($value)
    {
        $this->attributes['digital_clock'] = $value ?: null;
    }

    public function setAnalogClockAttribute($value)
    {
        $this->attributes['analog_clock'] = $value ?: null;
    }

    public function getDigitalClockAttribute($value)
    {
        if($value or $value === 0) {
            return $value;
        }else {
            return null;
        }
    }

    public function getAnalogClockAttribute($value)
    {
        if($value or $value === 0) {
            return $value;
        }else {
            return null;
        }
    }

    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function franchisee()
    {
        return $this->belongsTo('App\User', 'franchisee_id');
    }

    // searching scopes
    // (query, integer) [query]
    public function scopeSearchId($query, $id)
    {
         return $query->where('id', 'LIKE', '%'.$id.'%');
    }

    // (query, integer) [query]
    public function scopeSearchCustId($query, $cust_id)
    {
        return $query->whereHas('person', function($query) use ($cust_id){
            $query->where('cust_id', 'LIKE', '%'.$cust_id.'%');
        });
    }

    // (query, integer) [query]
    public function scopeSearchCompany($query, $company)
    {
        return $query->whereHas('person', function($query) use ($company){
            $query->where('company', 'LIKE', '%'.$company.'%')
                ->orWhere(function ($q) use ($company){
                    $q->where('name', 'LIKE', '%'.$company.'%')->where('cust_id', 'LIKE', 'D%');
            });
        });
    }

    // (query, integer) [query]
    public function scopeSearchProfile($query, $profile)
    {
        return $query->whereHas('person.profile', function($query) use ($profile){
            return $query->where('id', $profile);
        });
    }

    public function scopeSearchDateRange($query, $datefrom, $dateto)
    {
        $datefrom = Carbon::createFromFormat('d M y', $datefrom)->format('Y-m-d');
        $dateto = Carbon::createFromFormat('d M y', $dateto)->format('Y-m-d');
        return $query->where('delivery_date', '>=', $datefrom)->where('delivery_date', '<=', $dateto);
    }

    public function scopeSearchYearRange($query, $period)
    {
       if($period == 'this'){
            return $query->where('delivery_date', '>=', Carbon::now()->startOfYear()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::now()->endOfYear()->format('Y-m-d'));
       }else if($period == 'last'){
            return $query->where('delivery_date', '>=', Carbon::now()->subYear()->startOfYear()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));
       }
    }

    public function scopeSearchMonthRange($query, $month)
    {
        if($month != '0'){
            return $query->where('delivery_date', '>=', Carbon::create(Carbon::now()->year, $month)->startOfMonth()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::create(Carbon::now()->year, $month)->endOfMonth()->format('Y-m-d'));
        }else{
            return $query->where('delivery_date', '>=', Carbon::now()->startOfYear()->format('Y-m-d'))->where('delivery_date', '<=', Carbon::now()->endOfYear()->format('Y-m-d'));
        }
    }

}
