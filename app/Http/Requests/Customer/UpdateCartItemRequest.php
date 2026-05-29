<?php

namespace App\Http\Requests\Customer;

use App\Repositories\Customer\CustomerCartRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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

                $product = app(CustomerCartRepository::class)->findActiveProduct($this->route('product'));

                if ($this->integer('quantity') > (int) $product->inventory_qty) {
                    $validator->errors()->add('quantity', 'Requested quantity is greater than available stock.');
                }
            },
        ];
    }
}
