<?php

namespace App;

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
        'person_id', 'updated_by', 'name'
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
        'delivery_date' => 'Delivery Date',
        'order_date' => 'Order Date',
        'transremark' => 'Comment',
        'status' => 'Status',
        'pay_status' => 'Payment',
        'person_code'  => 'Customer',
        'updated_by' => 'Last Modified',
        'driver' => 'Driver',
        'del_address' => 'Delivery Address',
        'paid_by' => 'Payment Received By',
        'po_no' => 'PO #',
        'total_qty' => 'Total Qty',
    );    

    protected $fillable=[
        'total', 'delivery_date', 'status', 
        'user_id', 'transremark', 'updated_by',
        'pay_status', 'person_code', 'person_id',
        'order_date', 'driver', 'paid_by',
        'del_address', 'name', 'po_no', 
        'total_qty'
    ];

    protected $dates =[
        'created_at', 'delivery_date', 'order_date'
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
        return Carbon::parse($date)->format('d M y');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d M y h:i A');
    }    

    public function getDeliveryDateAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('d M y');    

        }else{

            return null;
        }
        
    }  

    public function getOrderDateAttribute($date)
    {
        if($date){

            return Carbon::parse($date)->format('d M y');    

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
