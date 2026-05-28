<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->items)) {
            $decodedItems = json_decode($this->items, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['items' => $decodedItems]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:users,id'],
            'shipping_fullname' => ['required', 'string', 'max:100'],
            'shipping_mobile' => ['required', 'string', 'max:15'],
            'payment_method' => ['required', 'integer', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'shipping_ward_id' => ['nullable', 'string', 'max:10'],
            'shipping_housenumber_street' => ['required', 'string', 'max:255'],
            'delivered_date' => ['nullable', 'date'],
            'discount_code' => ['nullable', 'string', 'exists:discounts,code'],
            'feeship_id' => ['nullable', 'integer', 'exists:transports,id'],
            'status' => ['required', Rule::in(Order::STATUSES)],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }
}
