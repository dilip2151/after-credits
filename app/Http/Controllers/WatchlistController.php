<?php

namespace App\Http\Controllers;

use App\Models\WatchlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function index(): View
    {
        $items = WatchlistItem::latest()->get();

        return view('watchlist.index', ['items' => $items]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        WatchlistItem::create($validated);

        return redirect('/watchlist');
    }

    public function markWatched(WatchlistItem $item): RedirectResponse
    {
        $item->watched = true;
        $item->save();

        return redirect('/watchlist');
    }
}