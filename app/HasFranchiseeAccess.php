<?php

namespace App;

trait HasFranchiseeAccess{

    // normal builder
    public function filterFranchiseeProfile($query)
    {
        return $query->whereHas('profile', function($query) {
            $query->filterUserProfile();
        });
    }

    public function filterFranchiseeTransactionDB($query)
    {
        if($this->isFranchisee()) {
            $franchisee_id = $this->getFranchiseeId();
            return $query->where('people.franchisee_id', $franchisee_id);
        }else {
            return $query;
        }

    }

    // return profile ids only()
    public function getFranchiseeId()
    {
        $franchisee_id = auth()->user()->id;

        return $franchisee_id;
    }

    private function isFranchisee()
    {
        $is_franchisee = auth()->user()->hasRole('franchisee');

        return $is_franchisee;
    }


}