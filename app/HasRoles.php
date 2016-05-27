<?php

namespace App;

trait HasRoles{

    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    //attach role id to user
    public function assignRole($role)
    {

        return $this->roles()->sync(

            Role::whereName($role)->firstOrFail()

        );

    }

    public function hasRole($role)
    {

        if(is_string($role)){

            return $this->roles->contains('name', $role);

        }

        return !! $role->intersect($this->roles)->count();
    }

}