<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CinemaFilmMatchController extends Controller
{
    public function show(Request $request, int $movie): JsonResponse
    {
        $coordinates = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $tmdbToken = config('services.tmdb.read_token');
        $movieGluKey = config('movieglu.api_key');
        $movieGluClient = config('movieglu.client');
        $movieGluAuthorization = config('movieglu.authorization');

        if (blank($tmdbToken) || blank($movieGluKey)
            || blank($movieGluClient) || blank($movieGluAuthorization)) {
            return response()->json([
                'message' => 'Cinema film matching is not configured.',
            ], 503);
        }

        try {
            $tmdbResponse = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->withToken($tmdbToken)
                ->acceptJson()
                ->timeout(10)
                ->get("https://api.themoviedb.org/3/movie/{$movie}", [
                    'append_to_response' => 'external_ids',
                ]);

            if ($tmdbResponse->notFound()) {
                return response()->json(['message' => 'Film not found.'], 404);
            }

            if (! $tmdbResponse->successful()) {
                return response()->json([
                    'message' => 'Could not load the film details from TMDB.',
                ], 502);
            }

            $title = $tmdbResponse->json('title');
            $imdbId = $tmdbResponse->json('external_ids.imdb_id')
                ?? $tmdbResponse->json('imdb_id');

            if (blank($title) || blank($imdbId)) {
                return response()->json(['editions' => []]);
            }

            $latitude = number_format((float) $coordinates['latitude'], 3, '.', '');
            $longitude = number_format((float) $coordinates['longitude'], 3, '.', '');

            $movieGluResponse = Http::withHeaders([
                'client' => $movieGluClient,
                'x-api-key' => $movieGluKey,
                'Authorization' => $movieGluAuthorization,
                'territory' => config('movieglu.territory'),
                'api-version' => 'v200',
                'geolocation' => "{$latitude};{$longitude}",
                'device-datetime' => now('UTC')->format('Y-m-d\TH:i:s.v\Z'),
            ])->timeout(15)->get('https://api-gate2.movieglu.com/filmLiveSearch/', [
                'query' => $title,
                'n' => 25,
            ]);
        } catch (ConnectionException) {
            return response()->json([
                'message' => 'Could not connect to the film listings service. Please try again.',
            ], 502);
        }

        if (! $movieGluResponse->successful()) {
            return response()->json([
                'message' => 'Could not search MovieGlu for cinema editions.',
            ], 502);
        }

        $editions = collect($movieGluResponse->json('films') ?? [])
            ->filter(fn (array $film) => ($film['imdb_title_id'] ?? null) === $imdbId)
            ->map(fn (array $film) => [
                'id' => $film['film_id'],
                'name' => $film['film_name'],
            ])
            ->unique('id')
            ->values();

        return response()->json(['editions' => $editions])
            ->header('Cache-Control', 'no-store');
    }
}
