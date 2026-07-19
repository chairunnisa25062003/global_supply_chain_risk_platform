<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name', 'official_name', 'capital', 'region', 'subregion',
        'population', 'currency_code', 'currency_name', 'languages',
        'flag', 'iso2', 'iso3', 'gdp', 'gdp_year', 'inflation', 'inflation_year',
    ];
}
