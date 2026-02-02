<?php

namespace App\Enums;

enum PaymentGatewayTypes: string
{
    case CREDIT_CARD = 'credit_card';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';
    case STRIPE = 'stripe';
    case RAZORPAY = 'razorpay';
    case SQUARE = 'square';
    case CASH_ON_DELIVERY = 'cash_on_delivery';
    case WALLET = 'wallet';

    /**
     * Get display name for the payment gateway
     */
    public function displayName(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'Credit Card',
            self::PAYPAL => 'PayPal',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::STRIPE => 'Stripe',
            self::RAZORPAY => 'Razorpay',
            self::SQUARE => 'Square',
            self::CASH_ON_DELIVERY => 'Cash on Delivery',
            self::WALLET => 'Digital Wallet',
        };
    }

    /**
     * Get description for the payment gateway
     */
    public function description(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'Pay using Visa, MasterCard, American Express',
            self::PAYPAL => 'Pay using your PayPal account',
            self::BANK_TRANSFER => 'Direct bank transfer',
            self::STRIPE => 'Secure payment via Stripe',
            self::RAZORPAY => 'Popular payment gateway in India',
            self::SQUARE => 'Payment processing by Square',
            self::CASH_ON_DELIVERY => 'Pay when you receive the product',
            self::WALLET => 'Pay using digital wallet (Apple Pay, Google Pay)',
        };
    }

    /**
     * Check if gateway supports refunds
     */
    public function supportsRefund(): bool
    {
        return match($this) {
            self::CREDIT_CARD, self::STRIPE, self::RAZORPAY,
            self::SQUARE, self::PAYPAL => true,
            self::BANK_TRANSFER, self::CASH_ON_DELIVERY,
            self::WALLET => false,
        };
    }

    /**
     * Check if gateway requires immediate payment
     */
    public function requiresImmediatePayment(): bool
    {
        return match($this) {
            self::CASH_ON_DELIVERY => false,
            default => true,
        };
    }

    /**
     * Get gateway configuration keys required
     */
    public function requiredConfigKeys(): array
    {
        return match($this) {
            self::CREDIT_CARD, self::STRIPE => ['api_key', 'api_secret', 'public_key'],
            self::PAYPAL => ['client_id', 'client_secret', 'mode'],
            self::RAZORPAY => ['key_id', 'key_secret'],
            self::SQUARE => ['access_token', 'location_id'],
            self::BANK_TRANSFER => ['account_number', 'bank_name', 'account_holder'],
            self::CASH_ON_DELIVERY => [],
            self::WALLET => ['merchant_id', 'callback_url'],
        };
    }

    /**
     * Get all available gateways
     */
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get all gateways with display information
     */
    public static function allWithDetails(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'display_name' => $case->displayName(),
                'description' => $case->description(),
                'supports_refund' => $case->supportsRefund(),
                'requires_immediate_payment' => $case->requiresImmediatePayment(),
            ];
        }, self::cases());
    }

    /**
     * Get enabled gateways (configurable)
     */
    public static function enabled(): array
    {
        $enabledGateways = config('payment.enabled_gateways', [
            self::CREDIT_CARD->value,
            self::PAYPAL->value,
            self::BANK_TRANSFER->value,
        ]);

        return array_filter(self::cases(), function ($gateway) use ($enabledGateways) {
            return in_array($gateway->value, $enabledGateways);
        });
    }

    /**
     * Check if gateway is valid
     */
    public static function isValid(string $gateway): bool
    {
        return in_array($gateway, self::all());
    }

    /**
     * Get gateway instance
     */
    public static function fromValue(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * Get gateway class name (following naming convention)
     */
    public function gatewayClass(): string
    {
        $className = str_replace('_', '', ucwords($this->value, '_'));
        return "App\\Architecture\\Services\\Payment\\Gateways\\{$className}Gateway";
    }
}
