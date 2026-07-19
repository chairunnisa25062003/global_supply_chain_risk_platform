<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'country_name', 'score', 'level',
        'weather_score', 'inflation_score', 'news_score', 'currency_score',
    ];
}
