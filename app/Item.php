<?php

namespace App;

use App\Scopes\ItemScope;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ItemScope);
    }

    protected $fillable=[
        'name', 'remark', 'unit', 'product_id', 'main_imgpath', 'sub_imgpath',
        'img_remain', 'main_imgcaption', 'publish', 'qty_now', 'qty_last', 'lowest_limit',
        'email_limit', 'qty_order', 'is_inventory', 'is_commission', 'desc_imgpath',
        'itemcategory_id', 'nutri_imgpath', 'is_healthier', 'is_halal', 'is_active',
        'productpage_desc', 'base_unit'
    ];

    public function transactions()
    {
        return $this->belongsToMany('App\Transaction');
    }

    public function price()
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

    public function invrecords()
    {
        return $this->hasMany('App\InvRecord');
    }

    public function onlineprice()
    {
        return $this->hasOne('App\OnlinePrice');
    }

    public function dtdprice()
    {
        return $this->hasOne('App\DtdPrice');
    }

    public function itemcategory()
    {
        return $this->belongsTo('App\Itemcategory');
    }

    public function fprice()
    {
        return $this->hasOne('App\Fprice');
    }

}
