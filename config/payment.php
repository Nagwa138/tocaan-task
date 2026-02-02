<?php

return [
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'credit_card'),

    'gateways' => [
        'credit_card' => [
            'api_key' => env('STRIPE_API_KEY'),
            'api_secret' => env('STRIPE_API_SECRET'),
            'test_mode' => env('STRIPE_TEST_MODE', true),
            'validation_rules' => [
                'card_number' => ['required', 'string', 'regex:/^\d{16}$/'],
                'card_holder' => ['required', 'string', 'max:255'],
                'expiry_month' => ['required', 'integer', 'between:1,12'],
                'expiry_year' => ['required', 'integer', 'min:' . date('Y')],
                'cvv' => ['required', 'string', 'regex:/^\d{3,4}$/'],
                'save_card' => ['nullable', 'boolean'],
            ],
            'validation_messages' => [
                'card_number.regex' => 'Card number must be 16 digits',
                'expiry_year.min' => 'Card cannot be expired',
                'cvv.regex' => 'CVV must be 3 or 4 digits',
            ],
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
            'validation_rules' => [
                'payer_email' => ['required', 'email'],
            ]
        ],
        'bank_transfer' => [
            'account_name' => env('BANK_ACCOUNT_NAME'),
            'account_number' => env('BANK_ACCOUNT_NUMBER'),
            'bank_name' => env('BANK_NAME'),
            'validation_rules' => [
                // rules
            ]
        ],
    ],

    'enabled_gateways' => [
        'credit_card',
        'paypal',
        'bank_transfer',
    ]
];
