<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'group_id', 'area_id'
    ];
    public function area()
    {
        return $this->belongsTo('App\Area');
    }
    public function postcodes()
    {
        return $this->hasMany('App\Postcode');
    }
}
