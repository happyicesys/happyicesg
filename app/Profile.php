<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profile';

    protected $fillable =[
        'name', 'address', 'email',
        'contact', 'alt_contact', 'roc_no',
        'header', 'logo', 'footer' 
    ];
}
