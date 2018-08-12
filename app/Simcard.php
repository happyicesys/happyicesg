<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Simcard extends Model
{

    protected $fillable = [
        'phone_no', 'telco_name', 'simcard_no',
        'vending_id', 'updated_by'
    ];

    // relationships
    public function vending()
    {
        return $this->hasOne('App\Vending');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }  
}
