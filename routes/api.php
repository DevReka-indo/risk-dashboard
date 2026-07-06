<?php

use App\Http\Controllers\Api\MonitoringController;
use Illuminate\Support\Facades\Route;

// Rute DPT & SMAP Monitoring yang kita buat
Route::get('/monitoring/dpt', [MonitoringController::class, 'getDptMonitoring']);
Route::get('/monitoring/smap', [MonitoringController::class, 'getSmapMonitoring']);
