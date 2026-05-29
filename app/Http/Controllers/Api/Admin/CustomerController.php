<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCustomerRequest;
use App\Services\Admin\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customers) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->customers->all(),
        ]);
    }

    public function show($customer): JsonResponse
    {
        return response()->json([
            'data' => $this->customers->find($customer),
        ]);
    }

    public function update(SaveCustomerRequest $request, $customer): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->customers->update($customer, $request->validated()),
        ]);
    }

    public function destroy($customer): JsonResponse
    {
        $this->customers->delete($customer);

        return response()->json(null, 204);
    }
}
