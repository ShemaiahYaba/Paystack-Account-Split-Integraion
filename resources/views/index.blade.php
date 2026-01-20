<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paystack Subaccount Changes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 1200px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: white;
            font-size: 2.5em;
            margin-bottom: 50px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .card-number {
            position: absolute;
            top: 15px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
        }

        h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 20px;
            padding-right: 60px;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .badge.old {
            background: #fee;
            color: #c33;
        }

        .badge.new {
            background: #efe;
            color: #3c3;
        }

        .code-section {
            margin: 20px 0;
        }

        .code-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        pre {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            overflow-x: auto;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        code {
            font-family: 'Courier New', Consolas, monospace;
            color: #333;
        }

        .highlight {
            background: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }

        .impact-box {
            background: #e7f3ff;
            border-left: 4px solid #0099ff;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }

        .impact-box strong {
            color: #0077cc;
            display: block;
            margin-bottom: 8px;
        }

        .impact-box ul {
            margin-left: 20px;
            color: #555;
        }

        .impact-box li {
            margin: 5px 0;
        }

        .arrow {
            text-align: center;
            font-size: 2em;
            color: #667eea;
            margin: 10px 0;
        }

        @media (max-width: 768px) {
            .cards-container {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔑 Key Changes for Subaccount Integration</h1>

        <div class="cards-container">
            <!-- CARD 1 -->
            <div class="card">
                <div class="card-number">1</div>
                <h2>Adding User State to Payload</h2>

                <div class="code-section">
                    <div class="code-label">❌ OLD CODE</div>
                    <span class="badge old">Before</span>
                    <pre><code>public static function preparePayload(array $data): array
{
    return [
        'public_key' => config('services.paystack.public_key'),
        'amount' => $data['amount'] * 100,
        'email' => $data['email'],
        'currency' => $data['currency'] ?? 'NGN',
        'callback_url' => $data['callback_url'] ?? route('payment.callback'),
        'reference' => $data['reference'] ?? 'ADC_' . strtoupper(Str::random(12)),
    ];
}</code></pre>
                </div>

                <div class="arrow">↓</div>

                <div class="code-section">
                    <div class="code-label">✅ NEW CODE</div>
                    <span class="badge new">After</span>
                    <pre><code>public static function preparePayload(array $data): array
{
    $payload = [
        'public_key' => config('services.paystack.public_key'),
        'amount' => $data['amount'] * 100,
        'email' => $data['email'],
        'currency' => $data['currency'] ?? 'NGN',
        'callback_url' => $data['callback_url'] ?? route('payment.callback'),
        'reference' => $data['reference'] ?? 'ADC_' . strtoupper(Str::random(12)),
    ];

    <span class="highlight">// NEW: Add subaccount if user state is provided
    if (isset($data['user_state'])) {
        $subaccountCode = self::getSubaccountForState($data['user_state']);

        if ($subaccountCode) {
            $payload['subaccount'] = $subaccountCode;
            $payload['bearer'] = 'subaccount';
        }
    }</span>

    return $payload;
}</code></pre>
                </div>

                <div class="impact-box">
                    <strong>💡 What This Does:</strong>
                    <ul>
                        <li>Checks if <code>user_state</code> is passed in the payment data</li>
                        <li>Looks up the corresponding subaccount code for that state</li>
                        <li>Adds <code>subaccount</code> parameter to Paystack payload</li>
                        <li>Sets <code>bearer: 'subaccount'</code> so the state pays fees (not main account)</li>
                    </ul>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="card">
                <div class="card-number">2</div>
                <h2>State-to-Subaccount Mapper</h2>

                <div class="code-section">
                    <div class="code-label">❌ OLD CODE</div>
                    <span class="badge old">Before</span>
                    <pre><code>// No mapping existed
// All payments went to central account</code></pre>
                </div>

                <div class="arrow">↓</div>

                <div class="code-section">
                    <div class="code-label">✅ NEW CODE</div>
                    <span class="badge new">After - New Method Added</span>
                    <pre><code><span class="highlight">/**
 * Get Paystack subaccount code for a given state
 */
public static function getSubaccountForState(string $state): ?string
{
    $subaccounts = config('subaccounts.paystack');

    return $subaccounts[$state] ?? null;
}</span></code></pre>
                </div>

                <div class="code-section">
                    <div class="code-label">🗂️ Config File (config/subaccounts.php)</div>
                    <pre><code>return [
    'paystack' => [
        'Lagos' => env('PAYSTACK_SUBACCT_LAGOS'),
        'FCT' => env('PAYSTACK_SUBACCT_FCT'),
        'Kano' => env('PAYSTACK_SUBACCT_KANO'),
        'Rivers' => env('PAYSTACK_SUBACCT_RIVERS'),
        // ... all 37 states mapped to their subaccount codes
    ],
];</code></pre>
                </div>

                <div class="impact-box">
                    <strong>💡 What This Does:</strong>
                    <ul>
                        <li>Creates a mapping between state names and Paystack subaccount codes</li>
                        <li>Looks up the correct subaccount code for any given state</li>
                        <li>Returns <code>null</code> if no subaccount is found (fallback to central account)</li>
                        <li>Subaccount codes are stored in <code>.env</code> for security and flexibility</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card" style="max-width: 100%;">
            <h2 style="text-align: center; padding-right: 0;">🎯 Complete Flow</h2>
            <div style="text-align: center; margin: 30px 0; font-size: 1.1em; line-height: 2;">
                <div style="display: inline-block; padding: 10px 20px; background: #e7f3ff; border-radius: 10px; margin: 5px;">
                    User from <strong>Lagos</strong> makes payment
                </div>
                <div style="color: #667eea; font-size: 1.5em;">↓</div>
                <div style="display: inline-block; padding: 10px 20px; background: #fff3cd; border-radius: 10px; margin: 5px;">
                    Backend calls <code>preparePayload(['user_state' => 'Lagos'])</code>
                </div>
                <div style="color: #667eea; font-size: 1.5em;">↓</div>
                <div style="display: inline-block; padding: 10px 20px; background: #d4edda; border-radius: 10px; margin: 5px;">
                    <code>getSubaccountForState('Lagos')</code> returns <code>'SUBACCT_xxxx'</code>
                </div>
                <div style="color: #667eea; font-size: 1.5em;">↓</div>
                <div style="display: inline-block; padding: 10px 20px; background: #f8d7da; border-radius: 10px; margin: 5px;">
                    Payload includes <code>subaccount: 'SUBACCT_xxxx'</code> + <code>bearer: 'subaccount'</code>
                </div>
                <div style="color: #667eea; font-size: 1.5em;">↓</div>
                <div style="display: inline-block; padding: 10px 20px; background: #e7f3ff; border-radius: 10px; margin: 5px;">
                    Paystack automatically routes 100% to <strong>Lagos subaccount</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
