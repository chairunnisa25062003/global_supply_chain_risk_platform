<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_name',
        'country_code',
        'capital',
        'region',
        'currency_code',
        'currency_name',
        'latitude',
        'longitude',
        'flag'
    ];
    
public function ports()
    {
        return $this->hasMany(Port::class);
    }
}