<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable=[
        'name', 'remark', 'unit',
        'product_id', 'main_imgpath', 'sub_imgpath',
        'img_remain', 'main_imgcaption', 'publish'
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

    public function images()
    {
        return $this->hasMany('App\ImageItem');
    }

}
