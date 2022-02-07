<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceTemplate extends Model
{
    protected $fillable = [
        'name',
        'remarks',
    ];

    // relationships
    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function priceTemplateItems()
    {
        return $this->hasMany(PriceTemplateItem::class)->orderBy('sequence');
    }
}
