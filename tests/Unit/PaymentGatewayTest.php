<?php

namespace Tests\Unit;

use App\Architecture\Services\Payment\Gateways\CreditCardGateway;
use App\Architecture\Services\Payment\Gateways\PayPalGateway;
use App\Enums\PaymentGatewayTypes;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    public function test_credit_card_gateway_can_be_instantiated(): void
    {
        $gateway = new CreditCardGateway();

        $this->assertInstanceOf(CreditCardGateway::class, $gateway);
        $this->assertEquals(PaymentGatewayTypes::CREDIT_CARD->value, $gateway->getName());
        $this->assertEquals('Credit Card', $gateway->getDisplayName());
    }


    public function test_credit_card_gateway_requires_card_holder(): void
    {
        $gateway = new CreditCardGateway();

        $result = $gateway->charge(100.00, [
            'order_id' => 1,
            // Missing card holder
        ]);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('card holder', $result['message']);
    }

    public function test_credit_card_gateway_can_process_charge(): void
    {
        $gateway = new CreditCardGateway();

        $result = $gateway->charge(100.00, [
            'order_id' => 1,
            'customer_email' => 'test@example.com',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('transaction_id', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('gateway_response', $result);

        // Check if transaction_id is generated for successful payments
        if ($result['success']) {
            $this->assertNotNull($result['transaction_id']);
            $this->assertStringStartsWith('cc_', $result['transaction_id']);
        }
    }

    public function test_credit_card_gateway_can_process_refund(): void
    {
        $gateway = new CreditCardGateway();

        $result = $gateway->refund('txn_123456', 50.00);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('refund_id', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('amount_refunded', $result);
        $this->assertStringStartsWith('ref_', $result['refund_id']);
    }

    public function test_credit_card_gateway_can_get_payment_status(): void
    {
        $gateway = new CreditCardGateway();

        $result = $gateway->getPaymentStatus('txn_123456');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('transaction_id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('last_updated', $result);
        $this->assertEquals('txn_123456', $result['transaction_id']);
    }

    public function test_paypal_gateway_can_be_instantiated(): void
    {
        $gateway = new PayPalGateway();

        $this->assertInstanceOf(PayPalGateway::class, $gateway);
        $this->assertEquals(PaymentGatewayTypes::PAYPAL, $gateway->getGatewayType());
        $this->assertEquals('PayPal', $gateway->getDisplayName());
    }

    public function test_paypal_gateway_can_process_charge(): void
    {
        $gateway = new PayPalGateway();

        $result = $gateway->charge(100.00, [
            'order_id' => 1,
            'payer_email' => 'test@example.com',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);

        // For successful payments, check transaction_id
        if ($result['success']) {
            $this->assertNotNull($result['transaction_id']);
            $this->assertStringStartsWith('paypal_', $result['transaction_id']);
            $this->assertArrayHasKey('payer_email', $result['gateway_response']);
        }
    }

    public function test_payment_gateway_types_enum_works_correctly(): void
    {
        $creditCardType = PaymentGatewayTypes::CREDIT_CARD;

        $this->assertEquals('credit_card', $creditCardType->value);
        $this->assertEquals('Credit Card', $creditCardType->displayName());
        $this->assertEquals('Pay using Visa, MasterCard, American Express', $creditCardType->description());
        $this->assertTrue($creditCardType->supportsRefund());
        $this->assertTrue($creditCardType->requiresImmediatePayment());

        // Test gateway class generation
        $gatewayClass = $creditCardType->gatewayClass();
        $this->assertEquals('App\\Architecture\\Services\\Payment\\Gateways\\CreditCardGateway', $gatewayClass);
    }

    public function test_payment_gateway_types_enabled_method_filters_correctly(): void
    {
        // Set config
        config(['payment.enabled_gateways' => ['credit_card', 'paypal']]);

        $enabledGateways = PaymentGatewayTypes::enabled();

        $this->assertIsArray($enabledGateways);
        $this->assertCount(2, $enabledGateways);

        $gatewayValues = array_map(fn($gateway) => $gateway->value, $enabledGateways);
        $this->assertContains('credit_card', $gatewayValues);
        $this->assertContains('paypal', $gatewayValues);
        $this->assertNotContains('bank_transfer', $gatewayValues);
    }

    public function test_gateway_factory_creates_correct_gateway_instance(): void
    {
        $gatewayType = PaymentGatewayTypes::CREDIT_CARD;
        $gatewayClass = $gatewayType->gatewayClass();

        $this->assertTrue(class_exists($gatewayClass));

        $gateway = new $gatewayClass();
        $this->assertInstanceOf(CreditCardGateway::class, $gateway);
    }
}
