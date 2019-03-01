<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliveryorder extends Model
{
    protected $fillable = [
        'job_type', 'po_no', 'submission_datetime', 'brands', 'quantities',
        'pickup_date', 'pickup_timerange', 'pickup_attn', 'pickup_contact', 'pickup_address',
        'pickup_postcode', 'pickup_comment', 'delivery_date1', 'delivery_timerange', 'delivery_attn',
        'delivery_contact', 'delivery_address', 'delivery_postcode', 'delivery_comment', 'transaction_id',
        'requester', 'from_happyice', 'to_happyice', 'pickup_location_name', 'delivery_location_name',
        'requester_name', 'requester_contact', 'requester_notification_emails'
    ];

    protected $dates = [
        'submission_datetime', 'pickup_date', 'delivery_date1'
    ];

    public function requestor()
    {
        $this->belongsTo('App\User', 'requester');
    }
}
