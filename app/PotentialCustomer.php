<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PotentialCustomer extends Model
{
    protected $fillable = [
        'name', 'attn_to', 'contact', 'address', 'postcode', 'remarks', 'custcategory_id', 'account_manager_id', 'created_by', 'updated_by', 'is_important', 'is_first', 'is_second', 'is_third', 'is_fourth', 'is_fifth', 'is_sixth'
    ];

    // relationships
    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function custcategory()
    {
        return $this->belongsTo(Custcategory::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // setter
    public function setIsFirstAttribute($value)
    {
        $this->attributes['is_first'] = $value === 1 ? true : false;
    }

    public function setIsSecondAttribute($value)
    {
        $this->attributes['is_second'] = $value === 1 ? true : false;
    }

    public function setIsThirdAttribute($value)
    {
        $this->attributes['is_third'] = $value === 1 ? true : false;
    }

    public function setIsFourthAttribute($value)
    {
        $this->attributes['is_fourth'] = $value === 1 ? true : false;
    }

    public function setIsFifthAttribute($value)
    {
        $this->attributes['is_fifth'] = $value === 1 ? true : false;
    }

    public function setIsSixthAttribute($value)
    {
        $this->attributes['is_sixth'] = $value === 1 ? true : false;
    }
}
