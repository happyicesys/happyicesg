<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variance extends Model
{
    protected $fillable = [
    	'datein', 'pieces', 'reason', 'person_id', 'updated_by'
    ];

    protected $dates = [
    	'datein'
    ];



}
