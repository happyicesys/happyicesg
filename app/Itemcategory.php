<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Itemcategory extends Model
{
    protected $fillable = [
        'name', 'desc', 'code'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
