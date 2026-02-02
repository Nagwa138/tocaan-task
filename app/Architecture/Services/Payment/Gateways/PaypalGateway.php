<?php

namespace App\Architecture\Services\Payment\Gateways;

use App\Architecture\Services\Payment\Contracts\IPaymentGateway;
use App\Enums\PaymentGatewayTypes;

class PaypalGateway implements IPaymentGateway
{
    protected PaymentGatewayTypes $gatewayType = PaymentGatewayTypes::PAYPAL;

    public function __construct(array $config = [])
    {
        // Config can be used for API keys, mode (sandbox/live), etc.
        $this->config = $config;
    }

    public function charge(float $amount, array $options = []): array
    {
        // Validate required PayPal options
        if (empty($options['payer_email'])) {
            return [
                'success' => false,
                'transaction_id' => null,
                'message' => 'Payer email is required for PayPal payments',
                'gateway_response' => [
                    'error_code' => 'MISSING_EMAIL',
                    'error_message' => 'Payer email address is required',
                ]
            ];
        }

        // Simulate PayPal processing with 90% success rate
        $success = rand(1, 10) <= 9;

        if ($success) {
            $transactionId = 'paypal_' . time() . '_' . uniqid();

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'PayPal payment processed successfully',
                'gateway_response' => [
                    'paypal_transaction_id' => 'PAY-' . strtoupper(uniqid()),
                    'payer_email' => $options['payer_email'],
                    'amount' => $amount,
                    'currency' => $options['currency'] ?? 'USD',
                    'status' => 'COMPLETED',
                    'timestamp' => now()->toISOString(),
                    'intent' => $options['intent'] ?? 'sale',
                    'links' => [
                        [
                            'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=' . $transactionId,
                            'rel' => 'approve',
                            'method' => 'GET',
                        ]
                    ],
                ]
            ];
        }

        // Simulate failure cases
        $errorCases = [
            [
                'code' => 'PAYER_CANNOT_PAY',
                'message' => 'Payer cannot pay for this transaction',
            ],
            [
                'code' => 'PAYMENT_DENIED',
                'message' => 'Payment denied by payer',
            ],
            [
                'code' => 'INTERNAL_SERVICE_ERROR',
                'message' => 'An internal service error occurred',
            ],
        ];

        $error = $errorCases[array_rand($errorCases)];

        return [
            'success' => false,
            'transaction_id' => null,
            'message' => 'PayPal payment failed: ' . $error['message'],
            'gateway_response' => [
                'error_code' => $error['code'],
                'error_message' => $error['message'],
                'timestamp' => now()->toISOString(),
            ]
        ];
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        // Simulate PayPal refund
        $refundId = 'ref_paypal_' . uniqid();

        return [
            'success' => true,
            'refund_id' => $refundId,
            'message' => 'PayPal refund processed successfully',
            'amount_refunded' => $amount,
            'gateway_response' => [
                'refund_id' => $refundId,
                'status' => 'COMPLETED',
                'amount_refunded' => $amount,
                'currency' => 'USD',
                'timestamp' => now()->toISOString(),
            ]
        ];
    }

    public function getPaymentStatus(string $transactionId): array
    {
        // Simulate PayPal status check
        $statuses = [
            'COMPLETED' => 'Payment completed successfully',
            'PENDING' => 'Payment is pending',
            'FAILED' => 'Payment failed',
            'REFUNDED' => 'Payment has been refunded',
        ];

        $status = array_rand($statuses);

        return [
            'transaction_id' => $transactionId,
            'status' => strtolower($status),
            'status_message' => $statuses[$status],
            'last_updated' => now()->toISOString(),
            'gateway_response' => [
                'id' => $transactionId,
                'status' => $status,
                'update_time' => now()->toISOString(),
            ]
        ];
    }

    public function getGatewayType(): PaymentGatewayTypes
    {
        return $this->gatewayType;
    }

    public function getDisplayName(): string
    {
        return $this->gatewayType->displayName();
    }

    /**
     * PayPal-specific: Create approval URL for redirect flow
     */
    public function createApprovalUrl(float $amount, array $options = []): string
    {
        $transactionId = 'paypal_' . uniqid();

        // Simulate creating a PayPal payment with approval URL
        return 'https://www.sandbox.paypal.com/checkoutnow?token=' . $transactionId;
    }
}
