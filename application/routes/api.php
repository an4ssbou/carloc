<?php

use App\Http\Controllers\GpsIngestionController;
use Illuminate\Support\Facades\Route;

Route::post('/gps/vehicles/{vehicle}/positions', [GpsIngestionController::class, 'store'])
    ->middleware('throttle:60,1')->name('gps.positions.store');
