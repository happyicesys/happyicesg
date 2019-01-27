<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait, HasProfileAccess;

    public static function boot()
    {
        parent::boot();
    }

    public function identifiableName()
    {
        return $this->title;
    }

    protected $dontKeepRevisionOf = array(
        'person_id', 'name', 'cancel_trace',
        'dtdtransaction_id', 'delivery_fee', 'is_required_analog', 'ftransaction_id', 'is_vending_generate',
        'is_deliveryorder'
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
        'driver' => 'Delivered By',
        'del_address' => 'Delivery Address',
        'paid_by' => 'Payment Received By',
        'paid_at' => 'Payment Received At',
        'po_no' => 'PO #',
        'total_qty' => 'Total Qty',
        'paid_by' => 'Payment Received By',
        'pay_method' => 'Payment Method',
        'note' => 'Note',
        'contact' => 'Contact',
        'del_postcode' => 'PostCode',
        'digital_clock' => 'Digital Clock',
        'analog_clock' => 'Analog Clock',
        'balance_coin' => 'Balance Coin',
        'is_freeze' => 'Date Freeze'
    );

    protected $fillable=[
        'total', 'delivery_date', 'status', 'transremark', 'updated_by',
        'pay_status', 'person_code', 'person_id', 'order_date', 'driver', 'paid_by',
        'del_address', 'name', 'po_no', 'total_qty', 'pay_method', 'note',
        'paid_at', 'cancel_trace', 'dtdtransaction_id', 'contact', 'del_postcode', 'delivery_fee',
        'bill_address', 'digital_clock', 'analog_clock', 'balance_coin', 'is_freeze',
        'is_required_analog', 'ftransaction_id', 'sales_count', 'sales_amount', 'is_vending_generate',
        'gst', 'is_gst_inclusive', 'gst_rate', 'is_deliveryorder', 'created_by'
    ];

    protected $dates =[
        'created_at', 'delivery_date', 'order_date', 'paid_at'
    ];
/*
    public function setDeliveryDateAttribute($date)
    {
        if($date){
            $this->attributes['delivery_date'] = Carbon::parse($date);
        }else{
            $this->attributes['delivery_date'] = null;
        }
    } */
/*
    public function setOrderDateAttribute($date)
    {
        if($date){
            $this->attributes['order_date'] = Carbon::parse($date);
        }else{
            $this->attributes['order_date'] = null;
        }
    } */

    public function setPaidAtAttribute($date)
    {
        if($date) {
            $this->attributes['paid_at'] = Carbon::parse($date);
        }else {
            $this->attributes['paid_at'] = null;
        }
    }

    public function setTransremarkAttribute($value)
    {
        $this->attributes['transremark'] = $value ?: null;
    }

    public function setDigitalClockAttribute($value)
    {
        $this->attributes['digital_clock'] = $value ?: null;
    }

    public function setAnalogClockAttribute($value)
    {
        $this->attributes['analog_clock'] = $value ?: null;
    }

    public function setBalanceCoinAttribute($value)
    {
        $this->attributes['balance_coin'] = $value ?: null;
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d M y');
    }

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

    public function getBalanceCoinAttribute($value)
    {
        if($value or $value === 0.00) {
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

    public function sale()
    {
        return $this->hasOne('App\Sale');
    }

    public function deals()
    {
        return $this->hasMany('App\Deal');
    }

    public function invattachments()
    {
        return $this->hasMany('App\Invattachment');
    }

    public function deliveryorder()
    {
        return $this->hasOne('App\Deliveryorder');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function transactionpersonassets()
    {
        return $this->hasMany('App\Transactionpersonasset');
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

    // (query, string) [query]
    public function scopeSearchStatus($query, $status)
    {
         return $query->where('status', 'LIKE', '%'.$status.'%');
    }

    // (query, string) [query]
    public function scopeSearchPayStatus($query, $pay_status)
    {
         return $query->where('pay_status', 'LIKE', '%'.$pay_status.'%');
    }

    // (query, string) [query]
    public function scopeSearchUpdatedBy($query, $updated_by)
    {
         return $query->where('updated_by', 'LIKE', '%'.$updated_by.'%');
    }

    public function scopeSearchUpdatedAt($query, $date)
    {
        $date = Carbon::parse($date);

        return $query->whereDate('updated_at', '=', date($date));
    }

    public function scopeSearchDeliveryDate($query, $date)
    {
        $date = Carbon::parse($date);

        return $query->whereDate('delivery_date', '=', date($date));
    }

    // (query, string) [query]
    public function scopeSearchDriver($query, $driver)
    {
         return $query->where('driver', 'LIKE', '%'.$driver.'%');
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

    public function scopeIsAnalog($query)
    {
        return $query->where('is_required_analog', 1);
    }

}
