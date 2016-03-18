<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageItem extends Model
{
    protected $table = 'image_item';

    protected $fillable = [
      'item_id', 'caption', 'path'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
