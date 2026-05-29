<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Services\Customer\CustomerCheckoutService;
use App\Support\PublicReactShell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CustomerCheckoutService $checkout,
        private readonly PublicReactShell $shell,
    ) {}

    public function show(Request $request): Response
    {
        $props = $this->checkout->props($request->session());

        return $this->shell->render('CustomerCheckoutPage', $props, $props['title']);
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->checkout->checkout($request->session(), $request->validated()),
        ], 202);
    }

    public function request(string $checkoutRequest): JsonResponse
    {
        return response()->json([
            'data' => $this->checkout->checkoutRequest($checkoutRequest),
        ]);
    }
}
