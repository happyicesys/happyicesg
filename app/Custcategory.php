<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Custcategory extends Model
{
    protected $fillable = [
    	'name', 'desc'
    ];

    // relationships
    public function people()
    {
    	return $this->belongsToMany('App\Person');
    }

    public function bomtemplates()
    {
    	return $this->hasMany('App\Bomtemplate');
    }

    public function bomvendings()
    {
        return $this->hasMany('App\Bomvending');
    }

    // scopes
    public function scopeNames($query, $value)
    {
        return $query->whereIn('name', $value);
    }

    public function scopeExcludeNames($query, $value)
    {
        return $query->whereNotIn('name', $value);
    }

    public function scopeCustId($query, $value)
    {
        return $query->whereIn('id', $value);
    }

    public function scopeExcludeCustId($query, $value)
    {
        return $query->whereNotIn('id', $value);
    }

    public function scopeCustcategory($query, $value)
    {
        if (count($value) == 1) {
            $value = [$value];
        }
        return $query->whereIn('id', $custcategories);
    }

    public function scopeExcludeCustcategory($query, $value)
    {
        if (count($value) == 1) {
            $value = [$value];
        }
        return $query->whereNotIn('id', $custcategories);
    }

}
