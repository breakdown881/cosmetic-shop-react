<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveFeeShipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'province_name' => ['required', 'string', 'max:255'],
            'province_type' => ['required', 'string', 'max:200'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }
}
