<?php

namespace App;

trait HasFranchiseeAccess{

    // get the person id of franchisee()
    public function getFranchiseeId()
    {
        if(auth()->user->hasRole('franchisee')) {
            return auth()->user->id;
        }
    }

    // normal builder
    public function filterFranchiseeProfile($query)
    {
        return $query->whereHas('profile', function($query) {
            $query->filterUserProfile();
        });
    }

    // db query builder
    public function filterUserDbProfile($query)
    {
        $profileIdArr = $this->searchUserProfileId();

        return $query->whereIn('profiles.id', $profileIdArr);
    }

    // return profile ids only()
    public function getUserProfileIdArray()
    {
        $profileIdArr = $this->searchUserProfileId();

        return $profileIdArr;
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