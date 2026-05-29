<?php

namespace App\Http\Requests\Customer;

use App\Models\Order;
use App\Repositories\Customer\CustomerCheckoutRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_fullname' => ['required', 'string', 'max:100'],
            'shipping_mobile' => ['required', 'string', 'max:15'],
            'shipping_ward_id' => ['nullable', 'string', 'max:10'],
            'shipping_housenumber_street' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'integer', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'feeship_id' => ['nullable', 'integer', 'exists:transports,id'],
            'discount_code' => ['nullable', 'string', 'exists:discounts,code'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->session()->get('customer_cart', []) === []) {
                    $validator->errors()->add('cart', 'Your cart is empty.');
                }

                if ($this->filled('discount_code')) {
                    $discount = app(CustomerCheckoutRepository::class)->discountByCode($this->string('discount_code'));

                    if ($discount && (
                        ($discount->starts_at && $discount->starts_at->isFuture())
                        || ($discount->expires_at && $discount->expires_at->isPast())
                    )) {
                        $validator->errors()->add('discount_code', 'Discount code is not active.');
                    }
                }
            },
        ];
    }
}
