<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invattachment extends Model
{
    protected $fillable = [
        'path', 'name'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }
}
