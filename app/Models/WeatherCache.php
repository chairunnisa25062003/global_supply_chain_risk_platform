<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $table = 'weather_cache';

    protected $fillable = [
        'location_name', 'latitude', 'longitude', 'temperature',
        'condition', 'precipitation', 'wind_speed', 'is_storm', 'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
        'is_storm'   => 'boolean',
    ];
}
