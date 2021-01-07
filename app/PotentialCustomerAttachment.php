<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PotentialCustomerAttachment extends Model
{
    protected $fillable = [
        'url'
    ];

    // relationships
    public function potentialCustomer()
    {
        return $this->belongsTo(PotentialCustomer::class);
    }
}
