<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\Customer\CustomerOrderRepository;

class CustomerOrderService
{
    public function __construct(
        private readonly CustomerNavigationService $navigationService,
        private readonly CustomerOrderRepository $orderRepository,
    ) {}

    public function historyProps(?User $customer): array
    {
        if (! $customer) {
            return [
                'title' => 'Đơn hàng của tôi',
                'navItems' => $this->navigationService->navItems(),
                'requiresAuth' => true,
                'orders' => [],
            ];
        }

        return [
            'title' => 'Đơn hàng của tôi',
            'navItems' => $this->navigationService->navItems(),
            'requiresAuth' => false,
            'orders' => $this->orderRepository
                ->forCustomer($customer->id)
                ->map(fn (Order $order) => $this->formatOrder($order))
                ->values(),
        ];
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'shipping_fullname' => $order->shipping_fullname,
            'shipping_mobile' => $order->shipping_mobile,
            'shipping_fee' => (int) $order->shipping_fee,
            'discount_amount' => (int) $order->discount_amount,
            'sub_total' => (int) $order->sub_total,
            'payment_total' => (int) $order->payment_total,
            'created_at' => optional($order->created_at)->toDateTimeString(),
            'items' => $order->items->map(fn (OrderItem $item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'qty' => (int) $item->qty,
                'unit_price' => (int) $item->unit_price,
                'total_price' => (int) $item->total_price,
            ])->values(),
        ];
    }
}
