<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

        protected $fillable = [
        'contact', 'alt_contact',
        'email', 'name', 'cust_id',
        'remark', 'area', 'del_postcode',
        'company', 'bill_address', 'del_address',
        'payterm', 'cost_rate', 'bill_postcode',
        'active', 'site_name'
        ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];  


/*    protected $casts = [
        'active' => 'boolean',
    ]; */     

    // set default nullable value upon detection
    public function setEmailAttribute($value) 
    {

        $this->attributes['email'] = $value ?: null;

    }       

    // set default nullable value upon detection
    public function setRemarkAttribute($value) 
    {

        $this->attributes['remark'] = $value ?: null;
        
    } 

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

    //select field populate selected
    public function getRoleListAttribute()
    {
        return $this->roles->lists('id')->all();
    } 

    public function getCreatedAtAttribute($date)
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

    public function transaction()
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

    public function price()
    {
        return $this->hasOne('App\Price');
    }    

    /**
     * search like name
     * @param $name in string
     * @return mixed
     */
    public function scopeSearchName($query, $name)
    {
        return $query->where('name', 'like', "%$name%");
    }


    /**
     * search like contact
     * @param $contact in number
     * @return mixed
     */
    public function scopeSearchContact($query, $contact)
    {
        return $query->where('contact', 'like', "%$contact%");
    }

    /**
     * @param $query
     * @param $email
     * @return mixed
     */
    public function scopeSearchEmail($query, $email)
    {
        return $query->where('email', 'like', "%$email%");
    }

    public function scopeSearchArea($query, $area)
    {
        return $query->where('area','=', $area);
    }         
   
}
