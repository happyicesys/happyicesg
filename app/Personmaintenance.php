<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personmaintenance extends Model
{
    protected $fillable = [
        'person_id', 'title', 'remarks', 'updated_by', 'is_refund',
        'refund_name', 'refund_bank', 'refund_contact', 'created_by', 'refund_account',
        'created_at'
    ];

    public function person()
    {
        return $this->belongsTo('App\Person');
    }
    
    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }    
}
