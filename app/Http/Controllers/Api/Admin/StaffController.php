<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAdminStaffRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;

class StaffController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Admin::latest()->get(),
            'meta' => [
                'roles' => Admin::ROLE,
            ],
        ]);
    }

    public function store(SaveAdminStaffRequest $request): JsonResponse
    {
        $staff = Admin::create($request->validated());

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $staff,
        ], 201);
    }

    public function show(Admin $staff): JsonResponse
    {
        return response()->json([
            'data' => $staff,
            'meta' => [
                'roles' => Admin::ROLE,
            ],
        ]);
    }

    public function update(SaveAdminStaffRequest $request, Admin $staff): JsonResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $staff->update($data);

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $staff,
        ]);
    }

    public function destroy(Admin $staff): JsonResponse
    {
        $staff->delete();

        return response()->json(null, 204);
    }
}
