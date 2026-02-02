<?php

namespace App\Http\Requests\API\Payment;

use App\Architecture\Repositories\Interfaces\IOrderRepository;
use App\Enums\PaymentGatewayTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentProcessRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        // Check if user can process payment for this order
        if (!$this->getOrderId()) return true;
        else return $this->user()->can('update', app(IOrderRepository::class)->first(['id' => $this->getOrderId()]));
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $baseRules = [
            'order' => ['required', 'exists:orders,id'],
            'method' => ['required', 'string', Rule::in(PaymentGatewayTypes::enabled())],
            'gateway_data' => 'nullable|array',
        ];

        if ($gatewayRules = config("payment.gateways.{$this->input('method')}.validation_rules")) {
            $rules = [];

            foreach ($gatewayRules as $field => $fieldRules) {
                $rules["gateway_data.{$field}"] = $fieldRules;
            }

            return array_merge($baseRules, $rules);
        }

        return $baseRules;
    }

    public function messages(): array
    {
        return [
            'method.in' => 'Payment method is not supported.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->route()->parameters);
    }

    public function getOrderId(): string|null
    {
        return $this->route('order');
    }

    public function getPaymentMethod()
    {
        return $this->input('method');
    }
}
