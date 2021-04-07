<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustcategoryGroup extends Model
{
    protected $fillable = [
        'name', 'desc'
    ];

    // relationships
    public function custcategories()
    {
        return $this->hasMany(Custcategory::class);
    }
}
