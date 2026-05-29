<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveDiscountRequest;
use App\Services\Admin\DiscountService;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    public function __construct(private readonly DiscountService $discounts) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->discounts->all(),
        ]);
    }

    public function store(SaveDiscountRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->discounts->create($request->validated()),
        ], 201);
    }

    public function show($discount): JsonResponse
    {
        return response()->json([
            'data' => $this->discounts->find($discount),
        ]);
    }

    public function update(SaveDiscountRequest $request, $discount): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->discounts->update($discount, $request->validated()),
        ]);
    }

    public function destroy($discount): JsonResponse
    {
        $this->discounts->delete($discount);

        return response()->json(null, 204);
    }
}
