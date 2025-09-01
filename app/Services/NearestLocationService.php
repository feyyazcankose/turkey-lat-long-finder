<?php

namespace App\Services;

use App\Models\LocalizationCity;
use App\Models\LocalizationTown;

class NearestLocationService
{
    /**
     * Find nearest city and town for given coordinates.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array{status:string,data:array{city:array|null,district:array|null}}
     */
    public function findNearest(float $latitude, float $longitude): array
    {
        $nearestCity = $this->findNearestCity($latitude, $longitude);

        $nearestTown = null;
        if ($nearestCity !== null) {
            $nearestTown = $this->findNearestTownInCity($latitude, $longitude, (int) $nearestCity['id']);
        }

        return [
            'status' => 'success',
            'data' => [
                'city' => $nearestCity,
                'district' => $nearestTown ?: [
                    'ilce_adi' => 'Bilinmiyor',
                    'lat' => null,
                    'lon' => null,
                    'distance' => null,
                ],
            ],
        ];
    }

    /**
     * Find nearest city using Haversine calculated in PHP.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    private function findNearestCity(float $latitude, float $longitude): ?array
    {
        $earthRadiusKm = 6371.0;

        $cities = LocalizationCity::query()
            ->select(['id', 'city_name', 'plate_no', 'phone_code', 'latitude', 'longitude'])
            ->where('status', 1)
            ->get();

        $minDistance = INF;
        $nearest = null;

        foreach ($cities as $city) {
            $distance = $this->haversineDistanceKm(
                $latitude,
                $longitude,
                (float) $city->latitude,
                (float) $city->longitude,
                $earthRadiusKm
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = [
                    'id' => (int) $city->id,
                    'plaka' => $city->plate_no,
                    'il_adi' => $city->city_name,
                    'lat' => (float) $city->latitude,
                    'lon' => (float) $city->longitude,
                    'distance' => $distance,
                ];
            }
        }

        return $nearest;
    }

    /**
     * Find nearest town within the given city.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int   $cityId
     * @return array|null
     */
    private function findNearestTownInCity(float $latitude, float $longitude, int $cityId): ?array
    {
        $earthRadiusKm = 6371.0;

        $towns = LocalizationTown::query()
            ->select(['id', 'town_name', 'latitude', 'longitude'])
            ->where('status', 1)
            ->where('city_id', $cityId)
            ->get();

        $minDistance = INF;
        $nearest = null;

        foreach ($towns as $town) {
            if ($town->latitude === null || $town->longitude === null) {
                continue;
            }

            $distance = $this->haversineDistanceKm(
                $latitude,
                $longitude,
                (float) $town->latitude,
                (float) $town->longitude,
                $earthRadiusKm
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = [
                    'id' => (int) $town->id,
                    'ilce_adi' => $town->town_name,
                    'lat' => (float) $town->latitude,
                    'lon' => (float) $town->longitude,
                    'distance' => $distance,
                ];
            }
        }

        return $nearest;
    }

    /**
     * Calculate Haversine distance in kilometers.
     */
    private function haversineDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2, float $earthRadiusKm = 6371.0): float
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
