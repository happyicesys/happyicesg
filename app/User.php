<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes, HasRoles;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'username', 'contact', 'can_access_inv', 'user_code', 'company_name',
        'bill_address', 'is_active', 'master_franchisee_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    // set default nullable value upon detection
    public function setEmailAttribute($value)
    {

        $this->attributes['email'] = $value ?: null;

    }

    /**
     * Setting attribute parser for password bcrypt
     * Shall be removed if register is not via UsersController
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    //select field populate selected
    public function getRoleListAttribute()
    {
        return $this->roles->lists('id')->all();
    }

    //relationships
    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function transSubscription()
    {
        return $this->hasOne('App\TransSubscription');
    }

    /**
     * User Responsible
     * @return User user responsible for the change
     */
    public function userResponsible()
    {
        $user_model = \Config::get('auth.model');
        return $user_model::find($this->user_id);
    }

    public function profiles()
    {
        return $this->belongsToMany('App\Profile');
    }

    public function franchises()
    {
        return $this->hasMany('App\Person', 'franchisee_id');
    }

    public function custcategories()
    {
        return $this->belongsToMany('App\Custcategory');
    }

    // scopes
    // db query builder
    public function scopeFilterUserFranchise($query)
    {
        $userIdArr = $this->searchUserFranchiseId();

        return $query->whereIn('id', $userIdArr);
    }

    public function scopeFilterUserDbFranchise($query)
    {
        $userIdArr = $this->searchUserFranchiseId();

        return $query->whereIn('users.id', $userIdArr);
    }

    public function scopeUserId($query, $value)
    {
        return $query->whereIn('id', $value);
    }

    // get the current auth user and return it ownself expect admin
    private function searchUserFranchiseId()
    {
        $userIdArr = [];

        if(auth()->user()->hasRole('franchisee') or auth()->user()->hasRole('hd_user')) {
            array_push($userIdArr, auth()->user()->id);
        }else if(auth()->user()->hasRole('subfranchisee')) {
            array_push($userIdArr, auth()->user()->master_franchisee_id);
        }else {
            $users = User::all();
            foreach($users as $user) {
                if($user->hasRole('franchisee') or $user->hasRole('hd_user') or $user->hasRole('watcher')) {
                    array_push($userIdArr, $user->id);
                }
            }
        }

        return $userIdArr;
    }

}
