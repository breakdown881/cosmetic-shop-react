<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Role::latest()->get(),
            'meta' => [
                'allowed_roles' => Role::ALLOWED_ROLES,
            ],
        ]);
    }

    public function store(SaveRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $role,
        ], 201);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => $role,
            'meta' => [
                'allowed_roles' => Role::ALLOWED_ROLES,
            ],
        ]);
    }

    public function update(SaveRoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $role,
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(null, 204);
    }
}
