<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeoController;

Route::get('/data/retail', [GeoController::class, 'getRetail']);
Route::get('/stats/merek', [GeoController::class, 'statsMerek']);
Route::post('/feature/zona', [GeoController::class, 'storeZona']);