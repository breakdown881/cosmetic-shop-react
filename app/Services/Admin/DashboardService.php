<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Repositories\Admin\DashboardRepository;

class DashboardService
{
    public function __construct(private readonly DashboardRepository $dashboard) {}

    public function payload(): array
    {
        return [
            'periods' => [
                ['key' => 'today', 'label' => __('translate.today')],
                ['key' => 'yesterday', 'label' => __('translate.yesterday')],
                ['key' => 'thisWeek', 'label' => __('translate.thisWeek')],
                ['key' => 'thisMonth', 'label' => __('translate.thisMonth')],
                ['key' => 'threeMonths', 'label' => __('translate.3month')],
                ['key' => 'thisYear', 'label' => __('translate.thisYear')],
            ],
            'metrics' => [
                ['key' => 'orders', 'label' => __('translate.orders'), 'value' => $this->dashboard->orderCount()],
                ['key' => 'revenue', 'label' => __('translate.revenue'), 'value' => $this->dashboard->revenue(), 'type' => 'currency'],
                ['key' => 'cancelledOrders', 'label' => __('translate.cancelledOrders'), 'value' => $this->dashboard->cancelledOrderCount()],
            ],
            'orders' => $this->dashboard->recentOrders()
                ->map(fn (Order $order) => $this->formatOrder($order))
                ->values(),
            'labels' => [
                'code' => __('translate.code'),
                'confirm' => __('translate.confirm'),
                'customerName' => __('translate.customerName'),
                'customerPhone' => __('translate.customerPhone'),
                'delete' => __('translate.delete'),
                'deliveryAddress' => __('translate.deliveryAddress'),
                'detail' => __('translate.detail'),
                'edit' => __('translate.edit'),
                'emptyOrders' => 'Chua co don hang.',
                'find' => __('translate.find'),
                'fromDate' => __('translate.fromDate'),
                'orderDate' => __('translate.orderDate'),
                'orders' => __('translate.orders'),
                'paymentMethod' => __('translate.paymentMethod'),
                'status' => __('translate.status'),
                'toDate' => __('translate.toDate'),
                'total' => __('translate.total'),
            ],
        ];
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'customerName' => $order->customer?->name ?? $order->shipping_fullname,
            'customerPhone' => $order->shipping_mobile,
            'status' => $order->status,
            'orderDate' => optional($order->created_at)->toDateTimeString(),
            'paymentMethod' => (int) $order->payment_method === 0 ? 'COD' : 'Bank',
            'total' => (int) $order->payment_total,
            'deliveryAddress' => $order->shipping_housenumber_street,
        ];
    }
}
