<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceTemplate extends Model
{
    protected $fillable = [
        'name',
        'remarks',
        'file',
    ];

    // relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function priceTemplateItems()
    {
        return $this->hasMany(PriceTemplateItem::class)->orderBy('sequence');
    }
}
