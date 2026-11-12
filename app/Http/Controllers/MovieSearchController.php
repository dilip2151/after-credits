<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MovieSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $token = config('services.tmdb.read_token');

        if (blank($token)) {
            return response()->json([
                'message' => 'Movie search is not configured.',
            ], 503);
        }

        try {
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get('https://api.themoviedb.org/3/search/movie', [
                    'query' => $validated['query'],
                    'include_adult' => 'false',
                ]);
        } catch (ConnectionException) {
            return response()->json([
                'message' => 'Could not connect to TMDB. Try again shortly.',
            ], 502);
        }

        if ($response->failed()) {
            return response()->json([
                'message' => 'TMDB could not complete the search. Try again shortly.',
            ], 502);
        }

        return response()->json([
            'results' => $response->json('results', []),
        ]);
    }
}