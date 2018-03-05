<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drawings extends Model
{
 	protected $fillable = [
 		'drawing_id', 'drawing_path', 'bomcategory_id', 'bomcomponent_id', 'bompart_id',
 		'bompartconsumable_id'
 	];

    public function bomcategory()
    {
        return $this->belongsTo('App\Bomcategory');
    }

    public function bomcomponent()
    {
        return $this->belongsTo('App\Bomcomponent');
    }

    public function bompart()
    {
        return $this->belongsTo('App\Bompart');
    }

    public function bompartconsumable()
    {
        return $this->belongsTo('App\Bompartconsumable');
    }
}
