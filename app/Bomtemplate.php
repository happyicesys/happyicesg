<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bomtemplate extends Model
{
    protected $fillable = [
        'custcategory_id', 'bompart_id', 'updated_by'
    ];

    public function custcategory()
    {
    	return $this->belongsTo('App\Custcategory');
    }

    public function bompart()
    {
    	return $this->belongsTo('App\Bompart');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
