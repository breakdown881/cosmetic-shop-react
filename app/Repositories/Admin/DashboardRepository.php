<?php

namespace App\Repositories\Admin;

use App\Models\Order;
use Illuminate\Support\Collection;

class DashboardRepository
{
    public function recentOrders(): Collection
    {
        return Order::query()
            ->with('customer:id,name,email')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function orderCount(): int
    {
        return Order::query()->count();
    }

    public function cancelledOrderCount(): int
    {
        return Order::query()->where('status', 'CANCELLED')->count();
    }

    public function revenue(): int
    {
        return (int) Order::query()
            ->where('status', '!=', 'CANCELLED')
            ->sum('payment_total');
    }
}
