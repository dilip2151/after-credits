<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CinemaShowtimesController extends Controller
{
    public function show(Request $request, int $film): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $latitude = number_format((float) $validated['latitude'], 3, '.', '');
        $longitude = number_format((float) $validated['longitude'], 3, '.', '');

        try {
            $response = Http::withHeaders([
                'client' => config('movieglu.client'),
                'x-api-key' => config('movieglu.api_key'),
                'Authorization' => config('movieglu.authorization'),
                'territory' => config('movieglu.territory'),
                'api-version' => 'v200',
                'geolocation' => "{$latitude};{$longitude}",
                'device-datetime' => now('UTC')->format('Y-m-d\TH:i:s.v\Z'),
            ])->timeout(15)->get('https://api-gate2.movieglu.com/filmShowTimes/', [
                'film_id' => $film,
                'date' => $validated['date'],
                'n' => 5,
            ]);
        } catch (ConnectionException) {
            return response()->json([
                'message' => 'Could not connect to MovieGlu. Please try again.',
            ], 502);
        }

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Cinema showtimes are unavailable right now.',
            ], 502);
        }

        return response()->json([
            'date' => $validated['date'],
            'cinemas' => $response->json('cinemas') ?? [],
        ])->header('Cache-Control', 'no-store');
    }
}