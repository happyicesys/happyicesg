<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'area_code', 'name'
    ];
    public function groups()
    {
        return $this->hasMany('App\Group');
    }
}
