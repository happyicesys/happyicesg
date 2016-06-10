<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifyManager extends Model
{
    protected $table = 'notifymanagers';

    protected $fillable = [
        'title', 'content', 'person_id'
    ];

    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
