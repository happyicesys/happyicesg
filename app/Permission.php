<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    
    protected $fillable = [
        'name', 'label', 'remark'
    ];

    // set default nullable value upon detection
    public function setRemarkAttribute($value) 
    {

        $this->attributes['remark'] = $value ?: null;

    }  

    //m2m relationship with permission
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }   
}
