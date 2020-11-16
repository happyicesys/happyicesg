<?php

namespace App\Traits;
use App\Custcategory;

trait HasCustcategoryAccess{

    // normal builder
    public function filterUserCustcategory($query)
    {
        return $query->whereHas('custcategory', function($query) {
            $query->filterUserCustcategory();
        });
    }

    // db query builder
    public function filterUserDbCustcategory($query)
    {
        $custcategoryIdArr = $this->searchUserCustcategoryId();

        return $query->whereIn('custcategories.id', $custcategoryIdArr);
    }

    // return custcategory ids only()
    public function getUserCustcategoryIdArray()
    {
        $custcategoryIdArr = $this->searchUserCustcategoryId();

        return $custcategoryIdArr;
    }

    // get the current auth user and return custcategory id
    private function searchUserCustcategoryId()
    {
        $custcategoryIdArr = [];

        $user_custcategories = auth()->user()->custcategories;

        if(count($user_custcategories) === 0) {
            $user_custcategories = Custcategory::all();
        }

        foreach($user_custcategories as $user_custcategory) {
            array_push($custcategoryIdArr, $user_custcategory->id);
        }

        return $custcategoryIdArr;
    }

}