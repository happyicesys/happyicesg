<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bomcategorycustcat extends Model
{
    protected $fillable = [
        'custcategory_id', 'bomcategory_id', 'updated_by'
    ];

    public function custcategory()
    {
    	return $this->belongsTo('App\Custcategory');
    }

    public function bomcategory()
    {
    	return $this->belongsTo('App\Bomcategory');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
