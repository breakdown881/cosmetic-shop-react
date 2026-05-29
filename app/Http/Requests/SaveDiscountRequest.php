<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $discount = $this->route('discount');
        $discountId = is_object($discount) ? $discount->id : $discount;

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('discounts', 'code')->ignore($discountId),
            ],
            'description' => ['required', 'string'],
            'is_fixed' => ['required', 'integer', 'in:0,1'],
            'discount_amount' => ['required', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}
