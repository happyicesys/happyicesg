<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PotentialCustomer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'attn_to', 'contact', 'address', 'postcode', 'remarks', 'custcategory_id', 'account_manager_id', 'created_by', 'updated_by', 'is_important'
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

    public function potentialCustomerAttachments()
    {
        return $this->hasMany(PotentialCustomerAttachment::class);
    }

    public function salesProgresses()
    {
        return $this->belongsToMany(SalesProgress::class, 'potential_customer_sales_progress', 'potential_customer_id', 'sales_progress_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
