<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Custcategory extends Model
{
    protected $fillable = [
    	'name', 'desc', 'map_icon_file', 'custcategory_group_id'
    ];

    const MAP_BASE_URL = 'http://maps.google.com/mapfiles/ms/micons/';

    const MAP_ICON_FILE = [
        'red' => 'red.png',
        'blue' => 'blue.png',
        'green' => 'green.png',
        'light-blue' => 'lightblue.png',
        'pink' => 'pink.png',
        'purple' => 'purple.png',
        'yellow' => 'yellow.png',
        'orange' => 'orange.png'
    ];

    // relationships
    public function custcategoryGroup()
    {
        return $this->belongsTo(CustcategoryGroup::class);
    }

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

    public function users()
    {
        return $this->belongsToMany('App\User');
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
