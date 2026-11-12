<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class MovieWatchProvidersController extends Controller
{
    private const PROVIDER_WEBSITES = [
        2 => 'https://tv.apple.com/gb',
        8 => 'https://www.netflix.com/gb/',
        9 => 'https://www.primevideo.com/movie?tr=gb',
        10 => 'https://www.primevideo.com/movie?tr=gb',
        35 => 'https://www.rakuten.tv/uk',
        130 => 'https://www.skystore.com/',
        192 => 'https://www.youtube.com/feed/storefront',
        591 => 'https://www.nowtv.com/gb/help/article/cinema-membership',
        1796 => 'https://www.netflix.com/gb/',
        1825 => 'https://www.primevideo.com/movie?tr=gb',
        1899 => 'https://www.hbomax.com/gb/en',
        2100 => 'https://www.primevideo.com/movie?tr=gb',
    ];

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
            return response()->json(['message' => 'Film not found.'], 404);
        }

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Watch options are unavailable right now. Please try again.',
            ], 502);
        }

        $providers = $response->json('results.GB') ?? [];
        $subscription = $providers['flatrate'] ?? [];
        $rent = $providers['rent'] ?? [];
        $buy = $providers['buy'] ?? [];
        $websites = [];

        foreach (array_merge($subscription, $rent, $buy) as $provider) {
            $website = self::PROVIDER_WEBSITES[$provider['provider_id']] ?? null;

            if ($website !== null) {
                $websites[$provider['provider_name']] = $website;
            }
        }

        return response()->json([
            'region' => 'GB',
            'subscription' => array_column($subscription, 'provider_name'),
            'rent' => array_column($rent, 'provider_name'),
            'buy' => array_column($buy, 'provider_name'),
            'link' => $providers['link'] ?? null,
            'websites' => (object) $websites,
        ]);
    }
}