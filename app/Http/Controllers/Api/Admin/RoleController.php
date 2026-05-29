<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveRoleRequest;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->roles->all(),
            'meta' => [
                'allowed_roles' => $this->roles->allowedRoles(),
            ],
        ]);
    }

    public function store(SaveRoleRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->roles->create($request->validated()),
        ], 201);
    }

    public function show($role): JsonResponse
    {
        return response()->json([
            'data' => $this->roles->find($role),
            'meta' => [
                'allowed_roles' => $this->roles->allowedRoles(),
            ],
        ]);
    }

    public function update(SaveRoleRequest $request, $role): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->roles->update($role, $request->validated()),
        ]);
    }

    public function destroy($role): JsonResponse
    {
        $this->roles->delete($role);

        return response()->json(null, 204);
    }
}
