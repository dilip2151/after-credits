<?php

namespace App\Http\Controllers;

use App\Models\WatchlistItem;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function index(): View
    {
        $items = WatchlistItem::latest()->get();

        return view('watchlist.index', ['items' => $items]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tmdb_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $item = WatchlistItem::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $item->id,
                'unwatched_count' => WatchlistItem::where('watched', false)->count(),
            ], 201);
        }

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

    public function matchFilm(Request $request, WatchlistItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'tmdb_id' => ['required', 'integer', 'min:1'],
        ]);

        $item->tmdb_id = $validated['tmdb_id'];
        $item->save();

        return redirect('/watchlist');
    }

    public function poster(WatchlistItem $item): RedirectResponse
    {
        $movieId = $item->tmdb_id;
        $token = config('services.tmdb.read_token');

        abort_if(!$movieId || blank($token), 404);

        $posterPath = Cache::remember(
            "tmdb:movie:{$movieId}:poster",
            now()->addDay(),
            function () use ($movieId, $token) {
                try {
                    $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                        ->withToken($token)
                        ->acceptJson()
                        ->timeout(5)
                        ->get("https://api.themoviedb.org/3/movie/{$movieId}");
                } catch (ConnectionException) {
                    return null;
                }

                if (!$response->successful()) {
                    return null;
                }

                $path = $response->json('poster_path');

                return is_string($path) && preg_match('~^/[\w-]+\.(jpe?g|png|webp)$~iD', $path)
                    ? $path
                    : false;
            },
        );

        abort_unless(is_string($posterPath), 404);

        return redirect()->away("https://image.tmdb.org/t/p/w185{$posterPath}");
    }

    public function destroy(WatchlistItem $item): RedirectResponse
    {
        $item->delete();

        return redirect('/watchlist');
    }
}