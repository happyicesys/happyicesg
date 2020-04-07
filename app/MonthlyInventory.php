<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthlyInventory extends Model
{
    protected $fillable = [
        'year', 'month'
    ];
}
