<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the settings for each payment gateway used in the system.
    | To add a new gateway, simply add a new entry with its configuration.
    |
    */

    'credit_card' => [
        'class' => \App\Services\PaymentGateway\CreditCardGateway::class,
        'api_key' => env('CREDIT_CARD_API_KEY', ''),
        'api_secret' => env('CREDIT_CARD_API_SECRET', ''),
        'sandbox' => env('CREDIT_CARD_SANDBOX', true),
    ],

    'paypal' => [
        'class' => \App\Services\PaymentGateway\PayPalGateway::class,
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
        'sandbox' => env('PAYPAL_SANDBOX', true),
    ],

    'bank_transfer' => [
        'class' => \App\Services\PaymentGateway\BankTransferGateway::class,
        'account_number' => env('BANK_ACCOUNT_NUMBER', ''),
        'bank_code' => env('BANK_CODE', ''),
        'sandbox' => env('BANK_TRANSFER_SANDBOX', true),
    ],

    'stripe' => [
        'class' => \App\Services\PaymentGateway\StripeGateway::class,
        'public_key' => env('STRIPE_PUBLIC_KEY', ''),
        'secret_key' => env('STRIPE_SECRET_KEY', ''),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
        'sandbox' => env('STRIPE_SANDBOX', true),
    ],
];
