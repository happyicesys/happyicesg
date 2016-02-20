<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsEvents extends Model
{
    protected $table = 'newsevents';

    protected $fillable = [
        'src', 'alt', 'order'
    ];

    
}
