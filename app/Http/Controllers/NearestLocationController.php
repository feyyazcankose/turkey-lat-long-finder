<?php

namespace App\Http\Controllers;

use App\Services\NearestLocationService;
use Illuminate\Http\Request;

class NearestLocationController extends Controller
{
    private NearestLocationService $service;

    public function __construct(NearestLocationService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request)
    {
        // $validated = $request->validate([
        //     'lat' => ['required', 'numeric', 'between:-90,90'],
        //     'lon' => ['required', 'numeric', 'between:-180,180'],
        // ]);

        // dd($validated['lat'], $validated['lon']);

        $result = $this->service->findNearest((float) $request->lat, (float) $request->lon);
        return response()->json($result);
    }
}
