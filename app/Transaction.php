<?php

namespace App;
// namespace MyApp\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }  

    public function identifiableName()
    {
        return $this->title;
    }  

    protected $dontKeepRevisionOf = array(
        'person_id'
    );        

    protected $revisionEnabled = true;

    //Remove old revisions (works only when used with $historyLimit)
    protected $revisionCleanup = true; 

    //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.
    protected $historyLimit = 500; 

    //storing new creation
    protected $revisionCreationsEnabled = true;

    //revision appear format name
    protected $revisionFormattedFieldNames = array(
        'delivery_from' => 'Delivery From',
        'delivery_to' => 'Delivery To',
        'order_from' => 'Order From',
        'order_to' => 'Order To',
        'transremark' => 'Comment',
        'status' => 'Status',
        'pay_status' => 'Payment',
        'person_code'  => 'Customer',
        'driver' => "Driver",
    );    

    protected $fillable=[
        'total', 'delivery_from', 'status', 
        'person_id', 'user_id', 'transremark',
        'pay_status', 'person_code', 'delivery_to',
        'order_from', 'order_to', 'driver'
    ];

    protected $dates =[
        'created_at', 'delivery_from', 'delivery_to', 'order_from', 'order_to'
    ];

    public function setDeliveryFromAttribute($date)
    {
        if($date){

            $this->attributes['delivery_from'] = Carbon::parse($date);

        }else{

            $this->attributes['delivery_from'] = null;

        }
    }

    public function setDeliveryToAttribute($date)
    {
        if($date){

            $this->attributes['delivery_to'] = Carbon::parse($date);

        }else{

            $this->attributes['delivery_to'] = null;

        }        
    }

    public function setOrderFromAttribute($date)
    {
        if($date){

            $this->attributes['order_from'] = Carbon::parse($date);

        }else{

            $this->attributes['order_from'] = null;

        }
    }

    public function setOrderToAttribute($date)
    {
        if($date){

            $this->attributes['order_to'] = Carbon::parse($date);

        }else{

            $this->attributes['order_to'] = null;

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

    public function sale()
    {
        return $this->hasOne('App\Sale');
    }

    public function deals()
    {
        return $this->hasMany('App\Deal');
    }    

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y');
    }

    public function getDeliveryFromAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('d-M-Y');    

        }else{

            return null;
        }
        
    }

    public function getDeliveryToAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('d-M-Y');    

        }else{

            return null;
        }
        
    }  

    public function getOrderFromAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('d-M-Y');    

        }else{

            return null;
        }
        
    } 

    public function getOrderToAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('d-M-Y');    

        }else{

            return null;
        }
        
    }         

    /*public function getDates()
    {
         substitute your list of fields you want to be auto-converted to timestamps here: 
        return array('created_at', 'updated_at', 'deleted_at', 'delivery_date');
    }*/ 

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
