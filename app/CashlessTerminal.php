<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CashlessTerminal extends Model
{
    const PROVIDERS = [
        '1'=>'Nayax',
        '2'=>'Castle',
        '3'=>'XVend',
        '4'=>'Auresys',
        '5'=>'Beeptech'
    ];
    protected $fillable = [
        'provider_id',
        'provider_name',
        'terminal_id',
        'start_date',
        'vending_id',
    ];

    // relationships
    public function vending()
    {
        return $this->hasOne('App\Vending');
    }

    // setter
    public function setStartDateAttribute($date)
    {
        $this->attributes['start_date'] = $date? Carbon::parse($date) : null;
    }

    public function setProviderIdAttribute($value)
    {
        if($value) {
            $this->attributes['provider_id'] = $value;
            $this->attributes['provider_name'] = CashlessTerminal::PROVIDERS[$value];
        }
    }

    // getter
    public function getStartDateAttribute($date)
    {
        if($date) {
            return Carbon::parse($date)->format('Y-m-d');
        }
    }
}
