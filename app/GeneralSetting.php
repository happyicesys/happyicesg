<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $table = 'generalsettings';

    protected $fillable = [
        'DTDCUST_EMAIL_CONTENT', 'INVOICE_FREEZE_DATE', 'internal_billing_prefix', 'country_region', 'home_url'
    ];

    protected $dates = [
    	'INVOICE_FREEZE_DATE'
    ];
}
