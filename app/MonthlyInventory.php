<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthlyInventory extends Model
{
    protected $fillable = [
        'cutoff_date', 'qty', 'unit_price', 'closing_value', 'item_id'
    ];
}
