<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddToCartRequest;
use App\Http\Requests\Customer\UpdateCartItemRequest;
use App\Services\Customer\CustomerCartService;
use App\Services\Customer\CustomerNavigationService;
use App\Support\PublicReactShell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    public function __construct(
        private readonly CustomerCartService $cart,
        private readonly CustomerNavigationService $navigation,
        private readonly PublicReactShell $shell,
    ) {}

    public function show(Request $request): Response
    {
        $props = $this->cart->props($request->session(), $this->navigation->navItems());

        return $this->shell->render('CustomerCartPage', $props, $props['title']);
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->cart->add(
                $request->session(),
                $request->integer('product_id'),
                $request->integer('quantity')
            ),
        ], 201);
    }

    public function update(UpdateCartItemRequest $request, string $product): JsonResponse
    {
        return response()->json([
            'data' => $this->cart->update(
                $request->session(),
                (int) $product,
                $request->integer('quantity')
            ),
        ]);
    }

    public function destroy(Request $request, string $product): JsonResponse
    {
        return response()->json([
            'data' => $this->cart->remove($request->session(), (int) $product),
        ]);
    }
}
