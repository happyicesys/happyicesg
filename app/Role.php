<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Role extends Model
{
    protected $fillable = [

        'name', 'label', 'remark'
        
    ];

    // set default nullable value upon detection
    public function setRemarkAttribute($value) 
    {
        $this->attributes['remark'] = $value ?: null;
    }    

    //m2m relationship with role
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class);
    }         

    //format output date as day-Month(string)-Year
    public function getCreatedAtAttribute($date)
    {   
        return Carbon::parse($date)->format('d-M-Y');
    }

    //select field populate selected
    public function getPermissionListAttribute()
    {
        return $this->permissions->lists('id')->all();
    }            
}
