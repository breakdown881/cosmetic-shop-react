<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly CustomerPaymentService $payments) {}

    public function vnpayReturn(Request $request): RedirectResponse
    {
        $order = $this->payments->handleVnpayReturn($request->query());

        return redirect('/orders/' . $order->id);
    }

    public function momoReturn(Request $request): RedirectResponse
    {
        $order = $this->payments->handleMomoResult($request->query());

        return redirect('/orders/' . $order->id);
    }

    public function momoIpn(Request $request): JsonResponse
    {
        $this->payments->handleMomoResult($request->all());

        return response()->json(['message' => 'OK']);
    }
}
