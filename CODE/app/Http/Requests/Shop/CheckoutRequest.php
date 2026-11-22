<?php

namespace App\Http\Requests\Shop;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Checks the checkout form (C-12, C-15).
 *
 * Form fields: customer_name, customer_email, customer_phone, shipping_address,
 * payment_method (cod | bank_transfer).
 */
class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // guests may check out too (R-20)
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in([Order::PAYMENT_COD, Order::PAYMENT_BANK_TRANSFER])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'The phone number must have 10 digits and start with 0.',
            'payment_method.in' => 'Please choose cash on delivery or bank transfer.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_name' => trim((string) $this->input('customer_name')),
            'customer_email' => strtolower(trim((string) $this->input('customer_email'))),
            'customer_phone' => preg_replace('/\s+/', '', (string) $this->input('customer_phone')),
            'shipping_address' => trim((string) $this->input('shipping_address')),
        ]);
    }

    /**
     * The delivery details, ready for OrderService::place().
     *
     * @return array{customer_name: string, customer_email: string, customer_phone: string, shipping_address: string}
     */
    public function customer(): array
    {
        return $this->safe()->only(['customer_name', 'customer_email', 'customer_phone', 'shipping_address']);
    }
}
