<?php

namespace App\Repositories\Customer;

use App\Models\Order;
use Illuminate\Support\Collection;

class CustomerOrderRepository
{
    public function forCustomer(int|string $customerId): Collection
    {
        return Order::query()
            ->with(['items.product:id,name,price'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    public function findForCustomer(int|string $customerId, int|string $orderId): Order
    {
        return Order::query()
            ->with(['items.product:id,name,price'])
            ->where('customer_id', $customerId)
            ->findOrFail($orderId);
    }

    public function cancel(Order $order): Order
    {
        $order->forceFill(['status' => 'CANCELLED'])->save();

        return $order->refresh()->load(['items.product:id,name,price']);
    }
}
