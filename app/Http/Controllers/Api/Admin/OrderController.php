<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveOrderRequest;
use App\Services\Admin\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->orders->all(),
        ]);
    }

    public function options(): JsonResponse
    {
        return response()->json($this->orders->options());
    }

    public function store(SaveOrderRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->orders->create($request->validated(), $request->user()?->id),
        ], 201);
    }

    public function show($order): JsonResponse
    {
        return response()->json([
            'data' => $this->orders->find($order),
        ]);
    }

    public function update(SaveOrderRequest $request, $order): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->orders->update($order, $request->validated()),
        ]);
    }

    public function destroy($order): JsonResponse
    {
        $this->orders->delete($order);

        return response()->json(null, 204);
    }
}
