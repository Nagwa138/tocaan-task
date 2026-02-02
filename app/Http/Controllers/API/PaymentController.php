<?php

namespace App\Http\Controllers\API;

use App\Architecture\Services\Interfaces\IPaymentService;
use App\Enums\PaymentGatewayTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Payment\PaymentListRequest;
use App\Http\Requests\API\Payment\PaymentProcessRequest;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * @param IPaymentService $paymentService
     */
    public function __construct(
        private readonly IPaymentService $paymentService,
    )
    {}


    /**
     * Store a newly created post with platforms
     */
    public function index(PaymentListRequest $request): JsonResponse
    {
        return $this->paymentService->list($request->getFilters(), $request->getPerPage());
    }

    public function gateways(): JsonResponse
    {
        $gateways = PaymentGatewayTypes::enabled();

        return response()->json([
            'data' => collect($gateways)->map(function ($gateway) {
                return [
                    'code' => $gateway->value,
                    'name' => $gateway->displayName(),
                    'description' => $gateway->description(),
                    'supports_refund' => $gateway->supportsRefund(),
                ];
            })->values()
        ]);
    }

    /**
     * Store a newly created post with platforms
     */
    public function process(PaymentProcessRequest $request): JsonResponse
    {
        return $this->paymentService->process($request->getOrderId(), $request->getPaymentMethod());
    }
}
