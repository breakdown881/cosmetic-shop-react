<?php

namespace App\Repositories\Customer;

use App\Models\Order;

class CustomerPaymentRepository
{
    public function findOrder(int|string $orderId): Order
    {
        return Order::query()->findOrFail($orderId);
    }

    public function markPending(Order $order, string $gateway): Order
    {
        $order->forceFill([
            'payment_gateway' => $gateway,
            'payment_status' => 'PENDING',
        ])->save();

        return $order->refresh();
    }

    public function markPaid(Order $order, string $reference): Order
    {
        $order->forceFill([
            'payment_status' => 'PAID',
            'payment_reference' => $reference,
            'paid_at' => now(),
            'status' => $order->status === 'PENDING' ? 'PROCESSING' : $order->status,
        ])->save();

        return $order->refresh();
    }

    public function markFailed(Order $order, ?string $reference = null): Order
    {
        $order->forceFill([
            'payment_status' => 'FAILED',
            'payment_reference' => $reference,
        ])->save();

        return $order->refresh();
    }
}
