<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = ['keyword', 'articles', 'fetched_at'];

    protected $casts = [
        'articles'   => 'array', 
        'fetched_at' => 'datetime',
    ];
}
