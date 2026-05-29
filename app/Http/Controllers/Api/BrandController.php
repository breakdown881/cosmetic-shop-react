<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Services\Api\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(private readonly BrandService $brands) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->brands->all($request),
        ]);
    }

    public function store(CreateBrandRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->brands->create(
                $request->validated(),
                $request->user('admin')?->id ?? $request->user()?->id,
                $request
            ),
        ], 201);
    }

    public function show($brand): JsonResponse
    {
        return response()->json([
            'data' => $this->brands->find($brand),
        ]);
    }

    public function update(CreateBrandRequest $request, $brand): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->brands->update($brand, $request->validated(), $request),
        ]);
    }

    public function destroy($brand): JsonResponse
    {
        $this->brands->delete($brand);

        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, $brand): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'integer', 'in:0,1,2'],
        ]);

        $brand->update(['status' => $data['status']]);

        return response()->json([
            'message' => __('translate.changeStatusSuccess'),
            'data' => $this->brands->updateStatus($brand, (int) $data['status']),
        ]);
    }
}
