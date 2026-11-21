<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeoController extends Controller
{
    private function retailPath(): string
    {
        return public_path('retail_south.geojson');
    }

    private function zonaPath(): string
    {
        return public_path('zona_retail.geojson');
    }

    // GET /api/data/retail
    public function getRetail()
    {
        $path = $this->retailPath();

        if (!file_exists($path)) {
            return response()->json(['error' => 'File GeoJSON tidak ditemukan'], 404);
        }

        $data = json_decode(file_get_contents($path), true);
        return response()->json($data);
    }

    // GET /api/stats/merek
    public function statsMerek()
    {
        $path = $this->retailPath();

        if (!file_exists($path)) {
            return response()->json(['error' => 'File GeoJSON tidak ditemukan'], 404);
        }

        $data = json_decode(file_get_contents($path), true);
        $stats = [];

        foreach ($data['features'] as $f) {
            $merek = $f['properties']['merek'] ?? 'Tidak diketahui';
            $stats[$merek] = ($stats[$merek] ?? 0) + 1;
        }

        return response()->json([
            'total'     => count($data['features']),
            'per_merek' => $stats,
        ]);
    }

    // POST /api/feature/zona
    public function storeZona(Request $request)
    {
        $feature = $request->all();

        if (($feature['type'] ?? '') !== 'Feature' ||
            ($feature['geometry']['type'] ?? '') !== 'Polygon') {
            return response()->json(
                ['error' => 'Body harus berupa GeoJSON Feature bertipe Polygon'],
                400
            );
        }

        $path = $this->zonaPath();
        $zona = file_exists($path)
            ? json_decode(file_get_contents($path), true)
            : ['type' => 'FeatureCollection', 'features' => []];

        $zona['features'][] = $feature;
        file_put_contents($path, json_encode($zona, JSON_PRETTY_PRINT));

        return response()->json([
            'message'    => 'Zona tersimpan',
            'total_zona' => count($zona['features']),
        ], 201);
    }
}