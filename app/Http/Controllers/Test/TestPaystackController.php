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
        // Simple test first
        return response()->json([
            'success' => true,
            'message' => 'Controller is working!',
            'request_data' => $request->all(),
        ]);

        // Original code commented out for debugging
        /*
        $validated = $request->validate([
            'state' => 'required|string',
            'email' => 'required|email',
            'amount' => 'required|numeric|min:100',
        ]);

        // Debug: Check if config is loaded
        $subaccounts = config('subaccounts.paystack');
        $selectedSubaccount = $subaccounts[$validated['state']] ?? null;

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
            'debug' => [
                'state_requested' => $validated['state'],
                'subaccount_from_config' => $selectedSubaccount,
                'config_loaded' => !empty($subaccounts),
            ],
        ]);
        */
    }

    public function verify($reference)
    {
        $service = new PaystackService();
        $result = $service->verify($reference);

        return view('test-paystack.result', compact('result'));
    }
}
