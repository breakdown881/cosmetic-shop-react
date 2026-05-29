<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerOrderService;
use App\Support\PublicReactShell;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly CustomerOrderService $orders,
        private readonly PublicReactShell $shell,
    ) {}

    public function index(Request $request): Response
    {
        $props = $this->orders->historyProps($request->user());

        return $this->shell->render('CustomerOrderHistoryPage', $props, $props['title']);
    }
}
