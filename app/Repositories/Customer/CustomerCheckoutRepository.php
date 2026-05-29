<?php

namespace App\Repositories\Customer;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerCheckoutRepository
{
    public function paymentMethods(): array
    {
        return Order::PAYMENT_METHODS;
    }

    public function activeFeeShips(): Collection
    {
        return Transport::query()
            ->with('province:id,name,type')
            ->latest()
            ->get();
    }

    public function feeShip(int|string|null $id): ?Transport
    {
        if (! $id) {
            return null;
        }

        return Transport::findOrFail($id);
    }

    public function discountByCode(?string $code): ?Discount
    {
        if (! $code) {
            return null;
        }

        return Discount::query()->where('code', $code)->first();
    }

    public function createOrderWithItems(array $orderData, array $items): Order
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = Order::create($orderData);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            return $order->load(['items.product:id,name,price', 'customer:id,name,email']);
        });
    }
}
