<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteTemplateItem extends Model
{
    protected $fillable = [
        'person_id', 'route_template_id', 'sequence'
    ];

    // relationships
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function routeTemplate()
    {
        return $this->belongsTo(RouteTemplate::class, 'route_template_id');
    }
}
