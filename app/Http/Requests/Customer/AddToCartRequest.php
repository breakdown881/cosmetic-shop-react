<?php

namespace App\Http\Requests\Customer;

use App\Repositories\Customer\CustomerCartRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $product = app(CustomerCartRepository::class)->findActiveProduct($this->integer('product_id'));

                if ($this->integer('quantity') > (int) $product->inventory_qty) {
                    $validator->errors()->add('quantity', 'Requested quantity is greater than available stock.');
                }
            },
        ];
    }
}
