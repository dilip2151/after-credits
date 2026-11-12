<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchlistItem extends Model
{
    protected $fillable = ['title', 'watched', 'tmdb_id'];

    protected $casts = [
        'watched' => 'boolean',
        'tmdb_id' => 'integer',
    ];
}
