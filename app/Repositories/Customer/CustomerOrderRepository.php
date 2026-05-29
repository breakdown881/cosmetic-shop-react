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
}
