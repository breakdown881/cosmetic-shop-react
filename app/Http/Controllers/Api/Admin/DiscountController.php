<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Discount::latest()
                ->get()
                ->map(fn (Discount $discount) => $this->formatDiscount($discount)),
        ]);
    }

    public function store(SaveDiscountRequest $request): JsonResponse
    {
        $discount = Discount::create($request->validated());

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->formatDiscount($discount),
        ], 201);
    }

    public function show(Discount $discount): JsonResponse
    {
        return response()->json([
            'data' => $this->formatDiscount($discount),
        ]);
    }

    public function update(SaveDiscountRequest $request, Discount $discount): JsonResponse
    {
        $discount->update($request->validated());

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->formatDiscount($discount->refresh()),
        ]);
    }

    public function destroy(Discount $discount): JsonResponse
    {
        $discount->delete();

        return response()->json(null, 204);
    }

    private function formatDiscount(Discount $discount): array
    {
        return [
            'id' => $discount->id,
            'code' => $discount->code,
            'description' => $discount->description,
            'is_fixed' => (int) $discount->is_fixed,
            'discount_amount' => (int) $discount->discount_amount,
            'starts_at' => optional($discount->starts_at)->format('Y-m-d H:i:s'),
            'expires_at' => optional($discount->expires_at)->format('Y-m-d H:i:s'),
            'created_at' => optional($discount->created_at)->toDateTimeString(),
            'updated_at' => optional($discount->updated_at)->toDateTimeString(),
        ];
    }
}
