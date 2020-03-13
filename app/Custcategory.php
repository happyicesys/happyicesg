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

    public function scopeIds($query, $value)
    {
        return $query->whereIn('id', $value);
    }

    public function scopeExcludeIds($query, $value)
    {
        return $query->whereNotIn('id', $value);
    }
}
