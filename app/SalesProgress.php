<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesProgress extends Model
{
    protected $fillable = [
        'name', 'order'
    ];

    // relationships
    public function potentialCustomers()
    {
        return $this->belongsToMany(PotentialCustomer::class, 'potential_customer_sales_progress', 'sales_progress_id', 'potential_customer_id');
    }
}
