<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $fillable =[
        'name', 'address', 'email',
        'contact', 'alt_contact', 'roc_no',
        'header', 'logo', 'footer',
        'gst', 'acronym', 'attn', 'is_gst_inclusive', 'gst_rate',
        'paynow_uen',

        'payterm_id', 'currency_id'
    ];

    public function people()
    {
        return $this->hasMany('App\Person');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function payterm()
    {
        return $this->belongsTo('App\Payterm');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    // getter and setter
/*     public function setGstRateAttribute($value)
    {
        if($value ){
            $this->attributes['gst_rate'] = $value;
        }else{
            $this->attributes['gst_rate'] = null;
        }
    } */

    // scopes
    // normal builder
    public function scopeFilterUserProfile($query)
    {
        $profileIdArr = $this->searchUserProfileId();

        return $query->whereIn('id', $profileIdArr);
    }

    // db query builder
    public function scopeFilterUserDbProfile($query)
    {
        $profileIdArr = $this->searchUserProfileId();

        return $query->whereIn('profiles.id', $profileIdArr);
    }

    public function scopeId($query, $value)
    {
        return $query->where('id', $value);
    }

    public function scopeName($query, $value)
    {
        return $query->where('name', $value);
    }

    // get the current auth user and return profiles id
    private function searchUserProfileId()
    {
        $profileIdArr = [];

        $user_profiles = auth()->user()->profiles;

        if($user_profiles) {
            foreach($user_profiles as $user_profile) {
                array_push($profileIdArr, $user_profile->id);
            }
        }

        return $profileIdArr;
    }
}
