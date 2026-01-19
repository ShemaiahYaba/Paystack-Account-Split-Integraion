<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paystack Subaccount Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold mb-4">Paystack Subaccount Integration Test</h1>

            <!-- Test Form -->
            <form id="testForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">User Email</label>
                    <input type="email" id="email" value="test@example.com"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Amount (NGN)</label>
                    <input type="number" id="amount" value="5000"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Select State</label>
                    <select id="state" class="w-full border rounded px-3 py-2">
                        <option value="">-- Select State --</option>
                        <!-- Will be populated by JavaScript -->
                    </select>
                </div>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Generate Payload
                </button>
            </form>
        </div>

        <!-- Output Display -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Generated Payload</h2>
            <div class="bg-gray-50 rounded p-4">
                <pre id="output" class="text-sm overflow-auto">Click "Generate Payload" to see results</pre>
            </div>

            <div class="mt-4 p-4 bg-blue-50 rounded">
                <h3 class="font-semibold mb-2">Subaccount Details:</h3>
                <pre id="subaccountInfo" class="text-sm">No state selected</pre>
            </div>
        </div>
    </div>

    <script>
        // Nigerian states and subaccount mapping (mock data - replace with actual codes)
        const stateSubaccounts = {
            'Abia': 'SUBACCT_abia_xxxxx',
            'Adamawa': 'SUBACCT_adamawa_xxxxx',
            'Akwa Ibom': 'SUBACCT_akwaibom_xxxxx',
            'Anambra': 'SUBACCT_anambra_xxxxx',
            'Bauchi': 'SUBACCT_bauchi_xxxxx',
            'Bayelsa': 'SUBACCT_bayelsa_xxxxx',
            'Benue': 'SUBACCT_benue_xxxxx',
            'Borno': 'SUBACCT_borno_xxxxx',
            'Cross River': 'SUBACCT_crossriver_xxxxx',
            'Delta': 'SUBACCT_delta_xxxxx',
            'Ebonyi': 'SUBACCT_ebonyi_xxxxx',
            'Edo': 'SUBACCT_edo_xxxxx',
            'Ekiti': 'SUBACCT_ekiti_xxxxx',
            'Enugu': 'SUBACCT_enugu_xxxxx',
            'FCT': 'SUBACCT_fct_xxxxx', // Federal Capital Territory (Abuja)
            'Gombe': 'SUBACCT_gombe_xxxxx',
            'Imo': 'SUBACCT_imo_xxxxx',
            'Jigawa': 'SUBACCT_jigawa_xxxxx',
            'Kaduna': 'SUBACCT_kaduna_xxxxx',
            'Kano': 'SUBACCT_kano_xxxxx',
            'Katsina': 'SUBACCT_katsina_xxxxx',
            'Kebbi': 'SUBACCT_kebbi_xxxxx',
            'Kogi': 'SUBACCT_kogi_xxxxx',
            'Kwara': 'SUBACCT_kwara_xxxxx',
            'Lagos': 'SUBACCT_lagos_xxxxx',
            'Nasarawa': 'SUBACCT_nasarawa_xxxxx',
            'Niger': 'SUBACCT_niger_xxxxx',
            'Ogun': 'SUBACCT_ogun_xxxxx',
            'Ondo': 'SUBACCT_ondo_xxxxx',
            'Osun': 'SUBACCT_osun_xxxxx',
            'Oyo': 'SUBACCT_oyo_xxxxx',
            'Plateau': 'SUBACCT_plateau_xxxxx',
            'Rivers': 'SUBACCT_rivers_xxxxx',
            'Sokoto': 'SUBACCT_sokoto_xxxxx',
            'Taraba': 'SUBACCT_taraba_xxxxx',
            'Yobe': 'SUBACCT_yobe_xxxxx',
            'Zamfara': 'SUBACCT_zamfara_xxxxx'
        };

        // Populate state dropdown
        const stateSelect = document.getElementById('state');
        Object.keys(stateSubaccounts).sort().forEach(state => {
            const option = document.createElement('option');
            option.value = state;
            option.textContent = state;
            stateSelect.appendChild(option);
        });

        // Handle form submission
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const amount = parseFloat(document.getElementById('amount').value);
            const state = document.getElementById('state').value;

            if (!state) {
                alert('Please select a state');
                return;
            }

            // Generate reference (mimicking Laravel Str::random)
            const reference = 'ADC_' + Math.random().toString(36).substr(2, 12).toUpperCase();

            // Get subaccount code
            const subaccountCode = stateSubaccounts[state];

            // Build payload (mimicking PaystackService::preparePayload)
            const payload = {
                public_key: 'pk_test_xxxxxxxxxxxxxx', // Mock key
                amount: amount * 100, // Convert to kobo
                email: email,
                currency: 'NGN',
                callback_url: 'https://yoursite.com/payment/callback',
                reference: reference,
                subaccount: subaccountCode, // NEW: This is what we're adding!
                bearer: 'subaccount' // NEW: Subaccount pays the fees (since they get 100%)
            };

            // Display output
            document.getElementById('output').textContent = JSON.stringify(payload, null, 2);

            // Display subaccount info
            const subaccountInfo = `
State: ${state}
Subaccount Code: ${subaccountCode}
Split: 100% to state (state pays Paystack fees)
Main Account: 0%

NOTE: Replace 'SUBACCT_${state.toLowerCase()}_xxxxx' with actual Paystack subaccount code
            `.trim();

            document.getElementById('subaccountInfo').textContent = subaccountInfo;
        });

        // Auto-select Lagos for quick testing
        document.getElementById('state').value = 'Lagos';
    </script>
</body>
</html>
