<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceItem extends Model
{
    protected $fillable = [
        'sequence',
        'status',
        'desc',
        'remarks',
        'created_by',
        'updated_by',
        'transaction_id',
        'attachment1',
        'attachment2',
    ];

    // relationships
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'modelable');
    }

    public function attachment1()
    {
        return $this->belongsTo(Attachment::class, 'attachment1');
    }

    public function attachment2()
    {
        return $this->belongsTo(Attachment::class, 'attachment2');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


}
