<?php

namespace App\Services\Members\Payment;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    public static function preparePayload(array $data): array
    {
        $publicKey = config('services.paystack.public_key');

        if (!$publicKey) {
            throw new \Exception('Paystack public key not configured. Please set PAYSTACK_PUBLIC_KEY in your .env file.');
        }

        // Fix the callback_url logic
        $callbackUrl = $data['callback_url'] ?? null;
        if (!$callbackUrl) {
            // Try to generate route, fallback to simple URL
            if (\Illuminate\Support\Facades\Route::has('payment.callback')) {
                $callbackUrl = route('payment.callback');
            } else {
                $callbackUrl = config('app.url') . '/payment/callback';
            }
        }

        $payload = [
            'public_key' => $publicKey,
            'amount' => $data['amount'] * 100, // Convert to kobo
            'email' => $data['email'],
            'currency' => $data['currency'] ?? 'NGN',
            'callback_url' => $callbackUrl,
            'reference' => $data['reference'] ?? 'ADC_' . strtoupper(Str::random(12)),
        ];

        // Add subaccount if user state is provided
        if (isset($data['user_state'])) {
            $subaccountCode = self::getSubaccountForState($data['user_state']);

            if ($subaccountCode) {
                $payload['subaccount'] = $subaccountCode;
                // Since states get 100% and pay fees themselves
                $payload['bearer'] = 'subaccount';

                Log::info("Payment split configured", [
                    'state' => $data['user_state'],
                    'subaccount' => $subaccountCode,
                    'reference' => $payload['reference']
                ]);
            } else {
                Log::warning("No subaccount found for state: {$data['user_state']}");
            }
        }

        return $payload;
    }

    /**
     * Get Paystack subaccount code for a given state
     */
    public static function getSubaccountForState(string $state): ?string
    {
        $subaccounts = config('subaccounts.paystack');

        return $subaccounts[$state] ?? null;
    }

    public function verify($reference)
    {
        $transaction = Transaction::where('reference', $reference)->first();

        if (!$transaction) {
            Log::warning("Transaction not found for reference: {$reference}");
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.paystack.co/transaction/verify/'.$reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.config('services.paystack.secret'),
                'Cache-Control: no-cache',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $tx = json_decode($response, true);

        if ($tx['status'] && $tx['data']['status'] == 'success') {
            $txData = $tx['data'];
            $authorization = $tx['data']['authorization'];

            return [
                'success' => true,
                'payment_gateway' => 'Paystack',
                'payment_gateway_json_response' => json_encode($tx),
                'payment_reference' => $reference,
                'payment_gateway_charge' => $txData['fees'],
                'payment_gateway_message' => $tx['message'],
                'payment_gateway_method' => $txData['channel'],
                'status' => 'Paid',
                'amount_paid' => $txData['amount'] / 100,
                // Add subaccount info if present
                'subaccount_code' => $txData['subaccount']['subaccount_code'] ?? null,
                'authorization' => [
                    'authorization_code' => $authorization['authorization_code'],
                    'card_type' => $authorization['card_type'],
                    'last4' => $authorization['last4'],
                    'channel' => $authorization['channel'],
                    'country_code' => $authorization['country_code'],
                    'payment_gateway' => 'Paystack',
                    'exp_month' => $authorization['exp_month'],
                    'exp_year' => $authorization['exp_year'],
                    'bin' => $authorization['bin'],
                    'bank' => $authorization['bank'],
                    'signature' => $authorization['signature'],
                    'reusable' => $authorization['reusable'],
                    'account_name' => $authorization['account_name'],
                ],
            ];
        } else {
            return [
                'success' => false,
                'payment_gateway' => 'Paystack',
                'status' => 'Failed',
                'payment_gateway_json_response' => json_encode($tx),
                'payment_gateway_message' => 'transaction failed on gateway',
            ];
        }
    }
}
