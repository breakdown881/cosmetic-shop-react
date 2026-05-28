<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCustomerRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::latest()->get()->map(fn (User $customer) => $this->formatCustomer($customer)),
        ]);
    }

    public function show(User $customer): JsonResponse
    {
        return response()->json([
            'data' => $this->formatCustomer($customer),
        ]);
    }

    public function update(SaveCustomerRequest $request, User $customer): JsonResponse
    {
        $customer->update($request->validated());

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->formatCustomer($customer->refresh()),
        ]);
    }

    public function destroy(User $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(null, 204);
    }

    private function formatCustomer(User $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'email_verified_at' => optional($customer->email_verified_at)->toDateTimeString(),
            'created_at' => optional($customer->created_at)->toDateTimeString(),
            'updated_at' => optional($customer->updated_at)->toDateTimeString(),
        ];
    }
}
