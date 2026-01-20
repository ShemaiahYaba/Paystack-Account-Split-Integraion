<?php

use App\Http\Controllers\Test\TestPaystackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::prefix('test-paystack')->group(function () {
    Route::get('/', [TestPaystackController::class, 'index']);
    Route::post('/initialize', [TestPaystackController::class, 'initialize']);
    Route::get('/verify/{reference}', [TestPaystackController::class, 'verify']);
});

// Add to routes/web.php
Route::get('/test-debug', function() {
    $service = new \App\Services\Members\Payment\PaystackService();

    return response()->json([
        'config_exists' => config('subaccounts.paystack') !== null,
        'paystack_config' => config('services.paystack'),
        'subaccounts' => config('subaccounts.paystack'),
        'lagos_subaccount' => config('subaccounts.paystack.Lagos'),
    ]);
});
