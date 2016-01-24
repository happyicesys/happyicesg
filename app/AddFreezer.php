<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddFreezer extends Model
{
    protected $table = 'addfreezers';
    
    protected $fillable = [
        'freezerqty', 'freezer_id', 'person_id'
    ];

    public function freezer()
    {
        return $this->belongsTo('App\Freezer');
    }

    public function person()
    {
        return $this->belongsTo('App\Person');
    }  

    // set default nullable value upon detection
    public function setFreezerqtyAttribute($value) 
    {
        if($value == null or $value == ''){

            $this->attributes['freezerqty'] = 1;    
        
        }else{

            $this->attributes['freezerqty'] = $value;
        } 
    }      
}
