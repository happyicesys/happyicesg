<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $fillable =[
        'name', 'address', 'email',
        'contact', 'alt_contact', 'roc_no',
        'header', 'logo', 'footer' ,
        'gst'
    ];

    public function people()
    {
        return $this->hasMany('App\Person');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
