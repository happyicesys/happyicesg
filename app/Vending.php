<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Chrisbjr\ApiGuard\Models\Mixins\Apikeyable;

class Vending extends Model
{
    use Apikeyable;
    
    protected $fillable = [
    	'vend_id', 'serial_no', 'type', 'router', 'desc'
    ];

    // relationships
    public function people()
    {
    	return $this->belongsToMany('App\Person');
    }
}
