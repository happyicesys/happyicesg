<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RackingConfig extends Model
{
    protected $fillable = [
        'name',
        'desc',
    ];

    // relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function vendings()
    {
        return $this->hasMany(Vending::class);
    }
}
