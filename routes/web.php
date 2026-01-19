<?php

use App\Http\Controllers\Test\TestPaystackController;
use Illuminate\Support\Facades\Route;

Route::prefix('test-paystack')->group(function () {
    Route::get('/', [TestPaystackController::class, 'index']);
    Route::post('/initialize', [TestPaystackController::class, 'initialize']);
    Route::get('/verify/{reference}', [TestPaystackController::class, 'verify']);
});

// Simple test route
Route::get('/test-route', function() {
    return response()->json(['message' => 'Routes are working!']);
});
