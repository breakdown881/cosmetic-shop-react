<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255|unique:categories,name,' . $this->route('category')?->id,
            'parent_id' => 'nullable|integer|min:1|exists:categories,id',
            'status'    => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => __('translate.required', ['attribute' => 'Name']),
            'name.max'          => __('translate.max.string', ['attribute' => 'Name', 'max' => 255]),
            'name.unique'       => __('translate.unique', ['attribute' => 'Name']),
            'parent_id.integer' => __('translate.parentId.integer'),
            'parent_id.exists'  => __('translate.parentId.integer'),
            'status.required'   => __('translate.required', ['attribute' => 'Status']),
        ];
    }

    public function attributes(): array
    {
        return [
            'name'      => __('translate.name'),
            'status'    => __('translate.status'),
        ];
    }
}
