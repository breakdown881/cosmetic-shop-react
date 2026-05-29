<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAdminStaffRequest;
use App\Services\Admin\StaffService;
use Illuminate\Http\JsonResponse;

class StaffController extends Controller
{
    public function __construct(private readonly StaffService $staff) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->staff->all(),
            'meta' => [
                'roles' => $this->staff->roles(),
            ],
        ]);
    }

    public function store(SaveAdminStaffRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->staff->create($request->validated()),
        ], 201);
    }

    public function show($staff): JsonResponse
    {
        return response()->json([
            'data' => $this->staff->find($staff),
            'meta' => [
                'roles' => $this->staff->roles(),
            ],
        ]);
    }

    public function update(SaveAdminStaffRequest $request, $staff): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->staff->update($staff, $request->validated()),
        ]);
    }

    public function destroy($staff): JsonResponse
    {
        $this->staff->delete($staff);

        return response()->json(null, 204);
    }
}
