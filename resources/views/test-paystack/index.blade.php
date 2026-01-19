<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paystack Subaccount Test</title>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #0099ff;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
        }
        select, input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #0099ff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #0077cc;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0099ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
            display: none;
        }
        .result.success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            display: block;
        }
        .result.error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            display: block;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Paystack Subaccount Split Test</h1>

        <div class="info-box">
            <strong>Test Purpose:</strong> Verify that payments are correctly routed to state-specific subaccounts
        </div>

        <form id="payment-form">
            @csrf

            <div class="form-group">
                <label for="state">Select State:</label>
                <select id="state" name="state" required>
                    <option value="">-- Choose a state --</option>
                    <option value="Lagos">Lagos</option>
                    <option value="FCT">FCT (Abuja)</option>
                    <option value="Kano">Kano</option>
                    <option value="Rivers">Rivers</option>
                    <!-- Add all 37 states -->
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="test@example.com" required>
            </div>

            <div class="form-group">
                <label for="amount">Amount (₦):</label>
                <input type="number" id="amount" name="amount" value="1000" min="100" required>
            </div>

            <button type="submit">Pay with Paystack</button>
        </form>

        <div id="result" class="result"></div>

        <div class="info-box" style="margin-top: 30px;">
            <strong>Expected Behavior:</strong>
            <ul>
                <li>Payment should be routed to the selected state's subaccount</li>
                <li>Subaccount code should appear in <code>payload.subaccount</code></li>
                <li>Bearer should be set to <code>subaccount</code> (state pays fees)</li>
                <li>After payment, verify in Paystack dashboard under Transaction Splits</li>
            </ul>
        </div>
    </div>

    <script>
        document.getElementById('payment-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const state = document.getElementById('state').value;
            const email = document.getElementById('email').value;
            const amount = document.getElementById('amount').value;

            try {
                // Call backend to prepare payment
                const response = await fetch('/test-paystack/initialize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ state, email, amount })
                });

                const data = await response.json();

                if (!data.success) {
                    showResult('error', data.message);
                    return;
                }

                // Show payload for debugging
                console.log('Paystack Payload:', data.payload);
                showResult('success', `
                    <strong>Payment Initialized</strong><br>
                    State: ${state}<br>
                    Subaccount: ${data.payload.subaccount || 'None (ERROR!)'}<br>
                    Reference: ${data.payload.reference}<br>
                    <small>Check console for full payload</small>
                `);

                // Initialize Paystack popup
                const handler = PaystackPop.setup({
                    key: data.payload.public_key,
                    email: data.payload.email,
                    amount: data.payload.amount,
                    currency: data.payload.currency,
                    ref: data.payload.reference,
                    subaccount: data.payload.subaccount,
                    bearer: data.payload.bearer,
                    callback: function(response) {
                        showResult('success', `
                            <strong>Payment Successful!</strong><br>
                            Reference: ${response.reference}<br>
                            <a href="/test-paystack/verify/${response.reference}">Verify Transaction</a>
                        `);
                    },
                    onClose: function() {
                        showResult('error', 'Payment cancelled');
                    }
                });

                handler.openIframe();

            } catch (error) {
                showResult('error', 'Error: ' + error.message);
            }
        });

        function showResult(type, message) {
            const resultDiv = document.getElementById('result');
            resultDiv.className = `result ${type}`;
            resultDiv.innerHTML = message;
        }
    </script>
</body>
</html>
