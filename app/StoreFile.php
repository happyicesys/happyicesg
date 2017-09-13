<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StoreFile extends Model
{
    protected $table = 'file_person';

    protected $fillable = [
        'path', 'name'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y');
    }

    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
