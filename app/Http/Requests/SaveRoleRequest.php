<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = $this->route('role');
        $roleId = is_object($role) ? $role->id : $role;

        return [
            'name' => [
                'required',
                'string',
                Rule::in(Role::ALLOWED_ROLES),
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
        ];
    }
}
