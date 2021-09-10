<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Operationdate extends Model
{
    protected $fillable = [
    	'delivery_date', 'color',
    	'person_id', 'created_by', 'deleted_by', 'transaction_id'
    ];

    // relationships
    public function person()
    {
    	return $this->belongsTo('App\Person');
    }

    public function transaction()
    {
    	return $this->belongsTo('App\Transaction');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function remover()
    {
        return $this->belongsTo('App\User', 'deleted_by');
    }

    // getter and setter
    public function getDeliveryDateAttribute($date)
    {
        return $date ? Carbon::parse($date)->format('Y-m-d') : null;
    }
}

