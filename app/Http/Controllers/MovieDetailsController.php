<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class MovieDetailsController extends Controller
{
    public function show(string $movie): JsonResponse
    {
        $token = config('services.tmdb.read_token');

        if (blank($token)) {
            return response()->json(['message' => 'Movie details are not configured.'], 503);
        }

        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get("https://api.themoviedb.org/3/movie/{$movie}");
        } catch (ConnectionException) {
            return response()->json(['message' => 'Could not connect to TMDB.'], 502);
        }

        if (!$response->successful()) {
            return response()->json(
                ['message' => 'Could not load movie details.'],
                $response->notFound() ? 404 : 502,
            );
        }

        $date = $response->json('release_date');

        return response()->json([
            'overview' => $response->json('overview'),
            'release_year' => is_string($date) ? substr($date, 0, 4) : null,
        ]);
    }
}
