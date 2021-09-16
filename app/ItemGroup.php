<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemGroup extends Model
{
    protected $fillable = [
        'name', 'desc',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
