<?php

namespace App\Repositories\Admin;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function all(): Collection
    {
        return Order::query()
            ->with(['customer:id,name,email', 'staff:id,name,email', 'items.product:id,name,price'])
            ->latest()
            ->get();
    }

    public function find(int|string $id): Order
    {
        return Order::findOrFail($id);
    }

    public function createWithItems(array $orderData, array $items): Order
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = Order::create($orderData);
            $this->syncItems($order, $items);

            return $order;
        });
    }

    public function updateWithItems(Order $order, array $orderData, array $items): Order
    {
        DB::transaction(function () use ($order, $orderData, $items) {
            $order->update($orderData);
            OrderItem::query()->where('order_id', $order->id)->delete();
            $this->syncItems($order, $items);
        });

        return $order->refresh();
    }

    public function deleteWithItems(Order $order): void
    {
        DB::transaction(function () use ($order) {
            OrderItem::query()->where('order_id', $order->id)->delete();
            $order->delete();
        });
    }

    public function loadForAdmin(Order $order): Order
    {
        return $order->load(['customer:id,name,email', 'staff:id,name,email', 'items.product:id,name,price']);
    }

    public function customersForOptions(): Collection
    {
        return User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    public function productsForOptions(): Collection
    {
        return Product::query()
            ->orderBy('name')
            ->get(['id', 'name', 'price']);
    }

    public function discountsForOptions(): Collection
    {
        return Discount::query()
            ->orderBy('code')
            ->get(['code', 'is_fixed', 'discount_amount']);
    }

    public function feeShipsForOptions(): Collection
    {
        return Transport::query()
            ->with('province:id,name,type')
            ->latest()
            ->get();
    }

    public function product(int|string $id): Product
    {
        return Product::findOrFail($id);
    }

    public function feeShip(int|string $id): Transport
    {
        return Transport::findOrFail($id);
    }

    public function discountByCode(?string $code): ?Discount
    {
        if (! $code) {
            return null;
        }

        return Discount::where('code', $code)->first();
    }

    private function syncItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $product = $this->product((int) $item['product_id']);
            $qty = (int) $item['qty'];

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'unit_price' => $product->price,
                'total_price' => $product->price * $qty,
            ]);
        }
    }
}
