<?php

namespace App\Architecture\Services\Payment\Gateways;

use App\Architecture\Services\Payment\Contracts\IPaymentGateway;

class CreditCardGateway implements IPaymentGateway
{
    public function __construct()
    {
        // Initialize with config from .env
        $this->apiKey = config('payment.credit_card.api_key');
        $this->apiSecret = config('payment.credit_card.api_secret');
    }

    public function charge(float $amount, array $options = []): array
    {
        // Simulate credit card processing
        // In real implementation, integrate with Stripe/Authorize.net etc.

        $success = rand(0, 1) === 1; // Simulate random success/failure

        if ($success) {
            $transactionId = 'cc_' . uniqid();

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Payment processed successfully',
                'gateway_response' => [
                    'auth_code' => strtoupper(uniqid()),
                    'amount_charged' => $amount,
                    'timestamp' => now()->toISOString(),
                ]
            ];
        }

        return [
            'success' => false,
            'transaction_id' => null,
            'message' => 'Credit card payment failed',
            'gateway_response' => [
                'error_code' => 'CC_DECLINED',
                'error_message' => 'Payment declined by bank'
            ]
        ];
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        // Simulate refund logic
        return [
            'success' => true,
            'refund_id' => 'ref_' . uniqid(),
            'message' => 'Refund processed successfully',
            'amount_refunded' => $amount
        ];
    }

    public function getPaymentStatus(string $transactionId): array
    {
        return [
            'transaction_id' => $transactionId,
            'status' => 'successful',
            'last_updated' => now()->toISOString()
        ];
    }

    public function getName(): string
    {
        return 'credit_card';
    }

    public function getDisplayName(): string
    {
        return 'Credit Card';
    }
}
