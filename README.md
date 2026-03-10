# Paystack Account Split Integration

A Laravel application that integrates Paystack's payment gateway with automatic account splitting across all 36 Nigerian states and the FCT. When a payment is initiated, it is routed to the appropriate state-level Paystack subaccount based on the payer's state, with transaction fees borne by the subaccount.

## How It Works

1. A payment is initiated with the payer's Nigerian state.
2. `PaystackService` looks up the Paystack subaccount code mapped to that state from `config/subaccounts.php`.
3. If a subaccount is found, it is added to the payment payload along with `bearer: subaccount` so that the subaccount absorbs the transaction fee.
4. The payload is used with the Paystack inline JS widget to collect payment on the frontend.
5. After payment, the reference is verified against the Paystack API and the result is stored in the `transactions` table.

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- A [Paystack](https://paystack.com) account with subaccounts created for each Nigerian state

## Setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Configure environment

Copy the example environment file and fill in your values:

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and set your Paystack credentials:

```env
PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxxxxxxxxxx
```

Then set the subaccount code for each state you operate in. Subaccount codes are created from your Paystack dashboard under **Settings → Subaccounts**.

```env
PAYSTACK_SUBACCT_LAGOS=SUBACCT_xxxxxxxxxxxxxxxx
PAYSTACK_SUBACCT_ABUJA=SUBACCT_xxxxxxxxxxxxxxxx
# ... repeat for all states
```

A full list of all 37 supported state variables is provided in `.env.example`.

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Build frontend assets

```bash
npm run build
```

Or for local development with hot reload:

```bash
composer run dev
```

## Configuration

### Paystack Credentials (`config/services.php`)

| Key | Environment Variable | Description |
|-----|---------------------|-------------|
| `services.paystack.public_key` | `PAYSTACK_PUBLIC_KEY` | Paystack public key (used in the frontend) |
| `services.paystack.secret` | `PAYSTACK_SECRET_KEY` | Paystack secret key (used for server-side verification) |

### State Subaccounts (`config/subaccounts.php`)

Each Nigerian state maps to a Paystack subaccount code. If a state has no configured subaccount, the payment proceeds without a split — the main account receives the full amount.

```php
// config/subaccounts.php
return [
    'paystack' => [
        'Lagos' => env('PAYSTACK_SUBACCT_LAGOS'),
        'Abuja' => env('PAYSTACK_SUBACCT_FCT'),
        // 36 states + FCT
    ],
];
```

**Supported states:** Abia, Adamawa, Akwa Ibom, Anambra, Bauchi, Bayelsa, Benue, Borno, Cross River, Delta, Ebonyi, Edo, Ekiti, Enugu, FCT, Gombe, Imo, Jigawa, Kaduna, Kano, Katsina, Kebbi, Kogi, Kwara, Lagos, Nasarawa, Niger, Ogun, Ondo, Osun, Oyo, Plateau, Rivers, Sokoto, Taraba, Yobe, Zamfara.

## Architecture

```
app/
├── Http/Controllers/Test/
│   └── TestPaystackController.php   # Test UI for payment flow
├── Models/
│   └── Transaction.php              # Polymorphic transaction record
└── Services/Members/Payment/
    └── PaystackService.php          # Core payment logic

config/
├── services.php                     # Paystack API credentials
└── subaccounts.php                  # State → subaccount code mapping

database/migrations/
└── ..._create_transactions_table.php
```

### `PaystackService`

| Method | Type | Description |
|--------|------|-------------|
| `preparePayload(array $data)` | `static` | Builds the Paystack payload; resolves the subaccount for the given `user_state` |
| `getSubaccountForState(string $state)` | `static` | Returns the subaccount code for a state, or `null` if unconfigured |
| `verify(string $reference)` | instance | Calls the Paystack verify API and returns a normalized result array |

#### `preparePayload` input

| Key | Required | Description |
|-----|----------|-------------|
| `email` | Yes | Payer's email address |
| `amount` | Yes | Amount in **Naira** (converted to kobo internally) |
| `currency` | No | Defaults to `NGN` |
| `user_state` | No | Nigerian state name; triggers subaccount split when provided |
| `callback_url` | No | Override the post-payment redirect URL |
| `reference` | No | Custom reference; auto-generated as `ADC_XXXXXXXXXXXX` if omitted |

### `Transaction` Model

Polymorphic — can belong to any model (donations, dues, etc.) via `transactionable`.

| Column | Description |
|--------|-------------|
| `transactionable_type/id` | Polymorphic owner |
| `amount` | Decimal amount in Naira |
| `reference` | Unique Paystack reference |
| `status` | `pending`, `success`, `failed`, `refunded` |
| `currency` | ISO 4217 currency code (default `NGN`) |
| `channel` | `card`, `bank_transfer`, etc. |
| `provider` | `paystack` |
| `subaccount_code` | Subaccount used for this transaction |
| `user_state` | Payer's state at payment time |
| `meta` | JSON blob for extra data |

## Routes

| Method | URL | Description |
|--------|-----|-------------|
| `GET` | `/` | Landing page |
| `GET` | `/test-paystack` | Test UI — form to initiate a payment |
| `POST` | `/test-paystack/initialize` | Returns Paystack payload JSON for the selected state |
| `GET` | `/test-paystack/verify/{reference}` | Verifies a transaction and displays the result |
| `GET` | `/test-debug` | Debug endpoint — shows loaded config and subaccounts |

## Test UI

Visit `/test-paystack` in your browser to use the built-in test interface. Select a state, enter an email and amount, and the form will initialize a Paystack payment routed to the correct subaccount.

## Running Tests

```bash
# All tests
php artisan test --compact

# Specific file
php artisan test --compact tests/Feature/ExampleTest.php
```

## Security

- Never expose `PAYSTACK_SECRET_KEY` on the frontend. It is only used server-side for transaction verification.
- Always verify transactions server-side via `/test-paystack/verify/{reference}` before fulfilling any order or membership.
- Rotate your Paystack keys immediately if they are ever committed to version control.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
