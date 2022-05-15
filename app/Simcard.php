<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Simcard extends Model
{
    const TELCOPROVIDERS = [
        'Singtel_IMSI' => 'Singtel (IMSI)',
        'Starhub_ICCID' => 'Starhub (ICCID)',
        'M1' => 'M1',
        'Redone' => 'Redone',
    ];

    protected $fillable = [
        'phone_no', 'telco_name', 'simcard_no',
        'vending_id', 'updated_by'
    ];


    public function identifiableName()
    {
        return $this->simcard_no.' - '.$this->telco_name;
    }

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
