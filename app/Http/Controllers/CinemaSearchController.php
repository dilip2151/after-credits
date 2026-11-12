<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CinemaSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $coordinates = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $latitude = (float) $coordinates['latitude'];
        $longitude = (float) $coordinates['longitude'];

        $directory = [
            [
                'name' => 'Everyman Birmingham Mailbox',
                'latitude' => 52.47555,
                'longitude' => -1.90565,
                'map_url' => 'https://www.openstreetmap.org/node/4683735545',
            ],
            [
                'name' => 'Cineworld Birmingham Broad Street',
                'latitude' => 52.47417,
                'longitude' => -1.91565,
                'map_url' => 'https://www.openstreetmap.org/node/259415101',
            ],
        ];

        $cinemas = collect($directory)
            ->map(fn (array $cinema) => [
                'name' => $cinema['name'],
                'map_url' => $cinema['map_url'],
                'distance_km' => $this->distanceKm(
                    $latitude,
                    $longitude,
                    $cinema['latitude'],
                    $cinema['longitude'],
                ),
            ])
            ->filter(fn (array $cinema) => $cinema['distance_km'] <= 10)
            ->sortBy('distance_km')
            ->values()
            ->map(function (array $cinema) {
                $cinema['distance_km'] = round($cinema['distance_km'], 1);

                return $cinema;
            });

        return response()->json(['results' => $cinemas]);
    }

    private function distanceKm(
        float $latitude,
        float $longitude,
        float $cinemaLatitude,
        float $cinemaLongitude,
    ): float {
        $latitudeDifference = deg2rad($cinemaLatitude - $latitude);
        $longitudeDifference = deg2rad($cinemaLongitude - $longitude);

        $a = sin($latitudeDifference / 2) ** 2
            + cos(deg2rad($latitude)) * cos(deg2rad($cinemaLatitude))
            * sin($longitudeDifference / 2) ** 2;

        return 6371 * 2 * asin(sqrt(min(1.0, $a)));
    }
}