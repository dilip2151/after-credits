<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchlistItem extends Model
{
    protected $fillable = ['title', 'watched'];

    protected $casts = [
        'watched' => 'boolean',
    ];
}