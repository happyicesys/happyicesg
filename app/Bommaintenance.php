<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bommaintenance extends Model
{
    protected $fillable = [
    	'maintenance_id', 'person_id', 'datetime', 'technician_id', 'urgency',
        'time_spend', 'bomcomponent_id', 'issue_type', 'solution', 'remark',
        'updated_by', 'created_by'
    ];

    // relationships
    public function person()
    {
    	return $this->belongsTo('App\Person');
    }

    public function bomcomponent()
    {
    	return $this->belongsTo('App\Bomcomponent');
    }

    public function technician()
    {
        return $this->belongsTo('App\User', 'technician_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

}
