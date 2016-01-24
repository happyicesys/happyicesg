<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddAccessory extends Model
{
    protected $table = 'addaccessories';

    protected $fillable = [
        'accessoryqty', 'accessory_id', 'person_id'
    ];

    public function accessory()
    {
        return $this->belongsTo('App\Accessory');
    }

    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    // set default nullable value upon detection
    public function setAccessoryqtyAttribute($value) 
    {
        if($value == null or $value == ''){

            $this->attributes['accessoryqty'] = 1;    
        
        }else{

            $this->attributes['accessoryqty'] = $value;
        } 
    } 

}
