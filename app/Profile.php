<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $fillable =[
        'name', 'address', 'email',
        'contact', 'alt_contact', 'roc_no',
        'header', 'logo', 'footer',
        'gst', 'acronym', 'attn', 'is_gst_inclusive',

        'payterm_id'
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
