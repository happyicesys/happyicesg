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

}
