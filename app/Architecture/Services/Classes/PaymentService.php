<?php

namespace App\Architecture\Services\Classes;

use App\Architecture\Repositories\Interfaces\IOrderRepository;
use App\Architecture\Repositories\Interfaces\IPaymentRepository;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Interfaces\IPaymentService;
use App\Architecture\Services\Payment\Contracts\IPaymentGateway;
use App\Enums\PaymentGatewayTypes;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Renderer\Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class PaymentService implements IPaymentService
{
    private IPaymentGateway $gateway;

    /**
     * @param IPaymentRepository $paymentRepository
     * @param IApiHttpResponder $apiHttpResponder
     * @param IOrderRepository $orderRepository
     */
    public function __construct(
        public IPaymentRepository $paymentRepository,
        public IApiHttpResponder $apiHttpResponder,
        private readonly IOrderRepository $orderRepository,
    )
    {
        $this->setGateway(config('payment.default_gateway'));
    }

    public function list(array $filters = [], int $perPage = 10): JsonResponse
    {
        try {
            $payments = $this->paymentRepository->paginate($filters, $perPage);
            return $this->apiHttpResponder->sendSuccess((new PaymentResource($payments))->toArray(request()), Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    /**
     *
     * @param string $orderId
     * @param string $paymentMethod
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function process(string $orderId, string $paymentMethod): JsonResponse
    {
//        DB::beginTransaction();

        try {
            // Step 1: Get the authenticated user's order with proper authorization
            $order = $this->orderRepository->first(['id' => $orderId]);

            $this->checkOrderIsConfirmed($orderId);

            // Step 2: Validate payment gateway using enum
            $gatewayType = $this->checkPaymentMethod($paymentMethod);

            $this->checkHasSuccessfulPayment($orderId);

            $this->checkForPaymentDuplication($orderId);

            // Step 3: Set up payment service with selected gateway
            $this->setGateway($paymentMethod);

            // Step 4: Call the gateway to process payment
            $gatewayResponse = $this->gateway->charge($order->total_amount, $this->preparePaymentData($order));

            // Step 5: Log payment attempt
            Log::info('Payment processing attempt', [
                'order_id' => $order->id,
                'gateway' => $gatewayType->value,
                'amount' => $order->total_amount,
                'currency' => $order->currency,
                'user_id' => $order->user_id,
            ]);

            // Step 6: Store the response from the gateway
            $payment = $this->paymentRepository->create([
                'payment_id' => $gatewayResponse['transaction_id'] ?? 'pending_' . uniqid(),
                'order_id' => $order->id,
                'status' => $gatewayResponse['success'] ? 'successful' : 'failed',
                'method' => $gatewayType->value,
                'amount' => $order->total_amount,
                'gateway_response' => $gatewayResponse,
                'gateway_metadata' => [
                    'gateway_type' => $gatewayType->value,
                    'gateway_display_name' => $gatewayType->displayName(),
                    'processed_at' => now()->toISOString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'platform' => request()->header('X-Platform', 'web'),
                    'client_reference' => request()->header('X-Client-Reference'),
                    'session_id' => session()->getId(),
                    'payer_info' => [
                        'email' => $order->user->email,
                        'name' => $order->user->name,
                    ],
                    'additional_data' => [
                        'return_url' => request()->return_url,
                        'items_count' => count($order->items),
                        'currency' => $order->currency ?? 'USD',
                    ],
                ],
            ]);

            if ($gatewayResponse['success']) {
                // TODO :: Trigger payment successful event
                // TODO :: Send payment confirmation notification
                // TODO :: Log::info('Payment successful');
            } else {
                $this->orderRepository->update(['id' => $orderId], ['status' => 'cancelled']);

                // TODO :: Trigger payment failed event

                Log::warning('Payment failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'gateway' => $gatewayType->value,
                    'error' => $gatewayResponse['message'] ?? 'Unknown error',
                ]);
            }

            // Step 7: Commit transaction
//            DB::commit();

            // Step 8: Prepare response data
            $responseData = [
                'data' => new PaymentResource($payment),
                'message' =>  ($gatewayResponse['success']
                    ? 'Payment processed successfully'
                    : 'Payment processing failed'),
                'code' => $gatewayResponse['code'] ?? ($gatewayResponse['success']
                        ? 'PAYMENT_SUCCESS'
                        : 'PAYMENT_FAILED'),
                'gateway_reference' => $gatewayResponse['transaction_id'] ?? null,
                'order_status' => $order->fresh()->status,
            ];

            // Step 9: Return appropriate HTTP response
            $statusCode = $gatewayResponse['success'] ? Response::HTTP_CREATED : Response::HTTP_PAYMENT_REQUIRED;

            return $this->apiHttpResponder->sendSuccess($responseData, $statusCode);
        } catch (Throwable $e) {
//            DB::rollBack();
            // TODO :: Log::critical('Payment processing critical error')

            if ($e instanceof ModelNotFoundException) {
                return $this->apiHttpResponder->sendError('Order not found', Response::HTTP_NOT_FOUND);
            } else if ($e instanceof ValidationException) {
                return $this->apiHttpResponder->sendError($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                return $this->apiHttpResponder->sendError('An unexpected error occurred while processing your payment', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    private function preparePaymentData(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer' => [
                'id' => $order->user->id,
                'email' => $order->user->email,
                'name' => $order->user->name,
                'phone' => $order->user->phone ?? null,
            ],
            'billing_address' => $order->billing_address ?? null,
            'shipping_address' => $order->shipping_address ?? null,
            'items' => $order->items,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'shipping' => $order->shipping_cost,
            'total_amount' => $order->total_amount,
            'currency' => $order->currency ?? config('app.currency', 'USD'),
            'description' => "Payment for Order #{$order->order_number}",
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'platform' => 'web',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            'gateway_specific' => request()->gateway_data ?? [],
            'return_url' => request()->return_url ?? config('app.url') . '/payment/callback',
            'webhook_url' => config('app.url') . '/api/v1/payments/webhook/',
        ];
    }
    private function setGateway(string $gateway): void
    {
        $gatewayType = PaymentGatewayTypes::tryFrom($gateway);
        $this->gateway = new ($gatewayType->gatewayClass());
    }

    /**
     * @throws Throwable
     */
    private function checkForPaymentDuplication(string $orderId): void
    {
        throw_if(
            $this->paymentRepository->existOrderPaymentRecently($orderId),
            ValidationException::withMessages([
                'order' => 'A payment attempt for this order was recently made. Please wait a few minutes.'
            ])
        );
    }

    /**
     * @throws Throwable
     */
    private function checkOrderIsConfirmed(string $orderId): void
    {
        throw_unless(
            $this->orderRepository->first(['id' => $orderId, 'status' => 'confirmed']),
            ValidationException::withMessages([
                'order' => 'The order is not ready to be paid.'
            ])
        );
    }

    /**
     * @throws Throwable
     */
    private function checkHasSuccessfulPayment(string $orderId): void
    {
        throw_if(
            $this->paymentRepository->first(['order_id' => $orderId, 'status' => 'successful']),
            ValidationException::withMessages(['order' => 'Order has been paid before.'])
        );
    }

    /**
     * @throws Throwable
     */
    private function checkPaymentMethod(string $method): ?PaymentGatewayTypes
    {
        $gatewayType = PaymentGatewayTypes::tryFrom($method);

        throw_unless($gatewayType, ValidationException::withMessages(['payment_method' => 'Invalid payment gateway selected']));

        return $gatewayType;
    }
}
