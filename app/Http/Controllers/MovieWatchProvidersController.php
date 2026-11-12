<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class MovieWatchProvidersController extends Controller
{
    public function show(int $movie): JsonResponse
    {
        $token = config('services.tmdb.read_token');

        if (blank($token)) {
            return response()->json([
                'message' => 'Watch options are not configured.',
            ], 503);
        }

        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get("https://api.themoviedb.org/3/movie/{$movie}/watch/providers");
        } catch (ConnectionException) {
            return response()->json([
                'message' => 'Could not connect to TMDB. Please try again.',
            ], 502);
        }

        if ($response->notFound()) {
            return response()->json([
                'message' => 'Film not found.',
            ], 404);
        }

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Watch options are unavailable right now. Please try again.',
            ], 502);
        }

        $providers = $response->json('results.GB') ?? [];

        return response()->json([
            'region' => 'GB',
            'subscription' => array_column($providers['flatrate'] ?? [], 'provider_name'),
            'rent' => array_column($providers['rent'] ?? [], 'provider_name'),
            'buy' => array_column($providers['buy'] ?? [], 'provider_name'),
            'link' => $providers['link'] ?? null,
        ]);
    }
}