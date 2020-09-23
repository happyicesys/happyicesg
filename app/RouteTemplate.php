<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteTemplate extends Model
{
    protected $fillable = [
        'name', 'desc', 'created_by', 'updated_by'
    ];

    // relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updater_by');
    }

    public function routeTemplateItems()
    {
        return $this->hasMany(RouteTemplateItem::class);
    }
}
