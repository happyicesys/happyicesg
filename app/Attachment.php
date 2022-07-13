<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable =[
        'url',
        'full_url',
        'is_primary',
        'is_title',
        'sequence',
        'modelable_id',
        'modelable_type',
    ];

    // relationships
    public function modelable()
    {
        return $this->morphTo();
    }

    public function getIsPrimaryAttribute($value)
    {
        return $value ? true : false;
    }
}
