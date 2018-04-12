<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Baum;

class Person extends Baum\Node
{

    use \Venturecraft\Revisionable\RevisionableTrait;
    use SoftDeletes;
    public static function boot()
    {
        parent::boot();
    }
    public function identifiableName()
    {
        return $this->title;
    }
    protected $dontKeepRevisionOf = array(
        'cust_id', 'profile_id', 'salutation',
        'user_id', 'parent_name', 'parent_id',
        'block', 'floor', 'unit', 'operation_note', 'del_lat',
        'del_lng', 'franchisee_id'
    );
    protected $revisionEnabled = true;
    protected $revisionCleanup = true;
    protected $historyLimit = 500;
    protected $revisionCreationsEnabled = true;
    protected $revisionFormattedFieldNames = array(
        'contact' => 'Contact Number',
        'alt_contact' => 'Alt Contact',
        'email' => 'Email',
        'name' => 'Att Name',
        'remark' => 'Remark',
        'del_postcode'  => 'Postcode',
        'company' => 'ID Name',
        'bill_address' => 'Billing Address',
        'del_address' => 'Delivery Address',
        'payterm' => 'Pay Term',
        'cost_rate' => 'Cost Rate',
        'active' => 'Active',
        'site_name' => 'Site Name',
        'com_remark' => 'Company',
        'cust_type' => 'Role Level',
        'time_range' => 'Available Time Range',
        'block_coverage' => 'Block Coverage',
        'custcategory_id' => 'Customer Category',
        'is_vending' => 'Fun Vending',
        'is_dvm' => 'Direct Vending',
        'vending_piece_price' => 'Piece/ Price',
        'vending_monthly_rental' => 'Monthly Rental',
        'vending_profit_sharing' => 'Profit Sharing',
        'vending_monthly_utilities' => 'Utility Fee',
        'vending_clocker_adjustment' => 'Clocker Adjustment',
        'is_profit_sharing_report' => 'Profit Sharing',
        'operation_note' => 'Operation Note',
        'is_gst_inclusive' => 'GST inclusive',
        'gst_rate' => 'GST Rate',
        'serial_number' => 'Serial Number'
    );

    protected $fillable = [
    'contact', 'alt_contact', 'com_remark', 'email', 'name', 'cust_id',
    'remark', 'del_postcode', 'company', 'bill_address', 'del_address',
    'payterm', 'cost_rate', 'active', 'site_name', 'profile_id',
    'note', 'salutation', 'dob', 'cust_type', 'user_id', 'parent_name',
    'parent_id', 'block', 'floor', 'unit', 'time_range', 'block_coverage',
    'custcategory_id', 'is_vending', 'vending_piece_price', 'vending_monthly_rental', 'vending_profit_sharing',
    'vending_monthly_utilities', 'vending_clocker_adjustment', 'is_profit_sharing_report', 'operation_note',
    'is_gst_inclusive', 'del_lat', 'del_lng', 'franchisee_id', 'gst_rate', 'is_dvm', 'serial_number'
    ];

    protected $dates = ['deleted_at'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function freezers()
    {
        return $this->belongsToMany(Freezer::class);
    }

    public function accessories()
    {
        return $this->belongsToMany(Accessory::class);
    }

    public function custcategory()
    {
        return $this->belongsTo('App\Custcategory');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function sale()
    {
        return $this->hasOne('App\Sale');
    }

    public function files()
    {
        return $this->hasMany('App\StoreFile');
    }

    public function payterm()
    {
        return $this->belongsTo('App\Payterm');
    }

    public function prices()
    {
        return $this->hasMany('App\Price');
    }

    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }

    public function notifymanagers()
    {
        return $this->hasMany('App\NotifyManager');
    }

    public function manager()
    {
        return $this->belongsTo('App\Person', 'parent_id');
    }

    public function postcodes()
    {
        return $this->hasMany('App\Postcode');
    }

    public function vendings()
    {
        return $this->belongsToMany('App\Vending');
    }

    public function operationdates()
    {
        return $this->hasMany('App\Operationdate');
    }

    public function franchisee()
    {
        return $this->belongsTo('App\User', 'franchisee_id');
    }

    public function fprices()
    {
        return $this->hasMany('App\Fprices');
    }

    public function bomvendings()
    {
        return $this->hasMany('App\Bomvending');
    }

    public function personmaintenances()
    {
        return $this->hasMany('App\Personmaintenance');
    }

    // getter and setter

    public function setDobAttribute($date)
    {
        $this->attributes['dob'] = $date? Carbon::parse($date) : null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ?: null;
    }

    public function setRemarkAttribute($value)
    {
        $this->attributes['remark'] = $value ?: null;
    }

    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = $value ?: null;
    }

    public function setCustcategoryIdAttribute($value)
    {
        $this->attributes['custcategory_id'] = $value ?: null;
    }

    public function getPostcodeListAttribute()
    {
        return $this->postcodes->lists('id')->toArray();
    }

    public function getRoleListAttribute()
    {
        return $this->roles->lists('id')->all();
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    public function getDobAttribute($date)
    {
        return Carbon::parse($date)->format('d-F-Y');
    }

    public function getFreezerListAttribute()
    {
        return $this->freezers->lists('id')->all();
    }

    public function getAccessoryListAttribute()
    {
        return $this->accessories->lists('id')->all();
    }

    public function getFullNameAttribute()
    {
        return $this->attribute['cust_id'].'-'.$this->attribute['name'];
    }

    // scopes
    public function scopeSearchName($query, $name)
    {
        return $query->where('name', 'like', "%$name%");
    }

    public function scopeSearchContact($query, $contact)
    {
        return $query->where('contact', 'like', "%$contact%");
    }

    public function scopeSearchEmail($query, $email)
    {
        return $query->where('email', 'like', "%$email%");
    }

    public function scopeSearchArea($query, $area)
    {
        return $query->where('area','=', $area);
    }

    public function scopeFilterFranchiseePeople($query)
    {
        $peopleIdArr = [];

        if(auth()->user()->hasRole('franchisee')) {
            $people = Person::where('franchisee_id', auth()->user()->id)->latest()->get();
        }else {
            $people = Person::all();
        }

        if(count($people) > 0) {
            foreach($people as $person) {
                array_push($peopleIdArr, $person->id);
            }
        }

        return $query->whereIn('people.id', $peopleIdArr);
    }

}
