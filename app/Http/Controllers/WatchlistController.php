<?php

namespace App\Http\Controllers;

use App\Models\WatchlistItem;
use Illuminate\Http\JsonResponse;
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
            'tmdb_id' => ['nullable', 'integer', 'min:1'],
        ]);

        WatchlistItem::create($validated);

        return redirect('/watchlist');
    }

    public function markWatched(Request $request, WatchlistItem $item): RedirectResponse|JsonResponse
    {
        $item->watched = true;
        $item->save();

        if ($request->expectsJson()) {
            return response()->json(['watched' => $item->watched]);
        }

        return redirect('/watchlist');
    }

    public function destroy(WatchlistItem $item): RedirectResponse
    {
        $item->delete();

        return redirect('/watchlist');
    }
}