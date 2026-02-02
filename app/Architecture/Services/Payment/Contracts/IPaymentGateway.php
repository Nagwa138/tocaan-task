<?php

namespace App\Architecture\Services\Payment\Contracts;

interface IPaymentGateway
{
    /**
     * Process a payment
     */
    public function charge(float $amount, array $options = []): array;

    /**
     * Refund a payment
     */
    public function refund(string $transactionId, ?float $amount = null): array;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): array;

    /**
     * Get display name
     */
    public function getDisplayName(): string;
}
