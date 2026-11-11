<?php

namespace App\Http\Controllers;

use App\Models\WatchlistItem;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function index(): View
    {
        $items = WatchlistItem::latest()->get();

        return view('watchlist.index', ['items' => $items]);
    }
}