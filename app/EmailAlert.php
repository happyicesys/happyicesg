<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailAlert extends Model
{
    protected $table = 'email_alert';

    protected $fillable = [
        'email'
    ];

    public function inventory()
    {
        return $this->belongsTo('App\Inventory');
    }
}
