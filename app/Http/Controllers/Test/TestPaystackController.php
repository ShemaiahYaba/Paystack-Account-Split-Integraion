<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Services\Members\Payment\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestPaystackController extends Controller
{
    public function index()
    {
        return view('test-paystack.index');
    }

    public function initialize(Request $request)
    {
        try {
            $validated = $request->validate([
                'state' => 'required|string',
                'email' => 'required|email',
                'amount' => 'required|numeric|min:100',
            ]);

            // Debug: Check if config is loaded
            $subaccounts = config('subaccounts.paystack');
            $selectedSubaccount = $subaccounts[$validated['state']] ?? null;

            // Log for debugging
            Log::info('Payment initialization attempt', [
                'state' => $validated['state'],
                'subaccount_code' => $selectedSubaccount,
                'config_exists' => !empty($subaccounts),
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
                'debug' => [
                    'state_requested' => $validated['state'],
                    'subaccount_from_config' => $selectedSubaccount,
                    'config_loaded' => !empty($subaccounts),
                    'total_states_configured' => count($subaccounts),
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Payment initialization error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verify($reference)
    {
        try {
            $service = new PaystackService();
            $result = $service->verify($reference);

            return view('test-paystack.result', compact('result'));

        } catch (\Exception $e) {
            Log::error('Payment verification error', [
                'reference' => $reference,
                'message' => $e->getMessage(),
            ]);

            return view('test-paystack.result', [
                'result' => [
                    'success' => false,
                    'message' => $e->getMessage(),
                ]
            ]);
        }
    }
}
