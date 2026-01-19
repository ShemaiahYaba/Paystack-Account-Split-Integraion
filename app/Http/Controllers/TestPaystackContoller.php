<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Services\Members\Payment\PaystackService;
use Illuminate\Http\Request;

class TestPaystackController extends Controller
{
    public function index()
    {
        return view('test-paystack.index');
    }

    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'state' => 'required|string',
            'email' => 'required|email',
            'amount' => 'required|numeric|min:100',
        ]);

        $payload = PaystackService::preparePayload([
            'email' => $validated['email'],
            'amount' => $validated['amount'],
            'user_state' => $validated['state'], // This triggers subaccount logic
            'currency' => 'NGN',
        ]);

        return response()->json([
            'success' => true,
            'payload' => $payload,
            'subaccount_found' => isset($payload['subaccount']),
        ]);
    }

    public function verify($reference)
    {
        $service = new PaystackService();
        $result = $service->verify($reference);

        return view('test-paystack.result', compact('result'));
    }
}
