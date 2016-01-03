<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable=[
        'name', 'remark', 'unit',
        'product_id'
    ];

    public function transactions()
    {
        return $this->belongsToMany('App\Transaction');
    }

    public function prices()
    {
        return $this->hasOne('App\Price');
    }    

    public function deals()
    {
        return $this->hasMany('App\Item');
    }    
      
}
