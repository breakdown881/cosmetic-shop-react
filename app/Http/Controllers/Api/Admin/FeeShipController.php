<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveFeeShipRequest;
use App\Services\Admin\FeeShipService;
use Illuminate\Http\JsonResponse;

class FeeShipController extends Controller
{
    public function __construct(private readonly FeeShipService $feeShips) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->feeShips->all(),
        ]);
    }

    public function store(SaveFeeShipRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->feeShips->create($request->validated()),
        ], 201);
    }

    public function show($feeship): JsonResponse
    {
        return response()->json([
            'data' => $this->feeShips->find($feeship),
        ]);
    }

    public function update(SaveFeeShipRequest $request, $feeship): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->feeShips->update($feeship, $request->validated()),
        ]);
    }

    public function destroy($feeship): JsonResponse
    {
        $this->feeShips->delete($feeship);

        return response()->json(null, 204);
    }
}
