<?php

namespace App\Services\Admin;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transport;
use App\Models\User;
use App\Repositories\Admin\OrderRepository;
use Illuminate\Support\Collection;

class OrderService
{
    public function __construct(private readonly OrderRepository $orders) {}

    public function all(): Collection
    {
        return $this->orders->all()
            ->map(fn (Order $order) => $this->format($order));
    }

    public function options(): array
    {
        return [
            'customers' => $this->orders->customersForOptions()
                ->map(fn (User $customer) => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                ])
                ->values(),
            'products' => $this->orders->productsForOptions()
                ->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                ])
                ->values(),
            'discounts' => $this->orders->discountsForOptions()
                ->map(fn (Discount $discount) => [
                    'code' => $discount->code,
                    'is_fixed' => (int) $discount->is_fixed,
                    'discount_amount' => (int) $discount->discount_amount,
                ])
                ->values(),
            'feeships' => $this->orders->feeShipsForOptions()
                ->map(fn (Transport $feeShip) => [
                    'id' => $feeShip->id,
                    'label' => trim(($feeShip->province?->type ? $feeShip->province->type.' ' : '').($feeShip->province?->name ?? 'Fee ship #'.$feeShip->id)),
                    'price' => $feeShip->price,
                ])
                ->values(),
            'statusOptions' => Order::STATUSES,
            'paymentMethods' => Order::PAYMENT_METHODS,
        ];
    }

    public function create(array $data, ?int $staffId): array
    {
        $order = $this->orders->createWithItems(
            $this->orderPayload($data, $staffId),
            $data['items']
        );

        return $this->format($this->orders->loadForAdmin($order));
    }

    public function find(int|string $id): array
    {
        return $this->format($this->orders->loadForAdmin($this->orders->find($id)));
    }

    public function update(int|string $id, array $data): array
    {
        $order = $this->orders->find($id);
        $order = $this->orders->updateWithItems(
            $order,
            $this->orderPayload($data, $order->staff_id),
            $data['items']
        );

        return $this->format($this->orders->loadForAdmin($order));
    }

    public function delete(int|string $id): void
    {
        $this->orders->deleteWithItems($this->orders->find($id));
    }

    private function orderPayload(array $data, ?int $staffId): array
    {
        $totals = $this->calculateTotals($data);

        return [
            'staff_id' => $staffId ?? 1,
            'customer_id' => $data['customer_id'],
            'shipping_fullname' => $data['shipping_fullname'],
            'shipping_mobile' => $data['shipping_mobile'],
            'payment_method' => $data['payment_method'],
            'shipping_ward_id' => $data['shipping_ward_id'] ?? '',
            'shipping_housenumber_street' => $data['shipping_housenumber_street'],
            'shipping_fee' => $totals['shipping_fee'],
            'feeship_id' => $data['feeship_id'] ?? null,
            'delivered_date' => $data['delivered_date'] ?? now()->toDateString(),
            'price_total' => $totals['sub_total'],
            'discount_code' => $data['discount_code'] ?? '',
            'discount_amount' => $totals['discount_amount'],
            'sub_total' => $totals['sub_total'],
            'tax' => 0,
            'price_inc_tax_total' => $totals['sub_total'],
            'voucher_code' => '',
            'voucher_amount' => 0,
            'payment_total' => $totals['payment_total'],
            'status' => $data['status'],
            'note' => $data['note'] ?? null,
        ];
    }

    private function calculateTotals(array $data): array
    {
        $subTotal = collect($data['items'])->sum(function (array $item) {
            $product = $this->orders->product((int) $item['product_id']);

            return $product->price * $item['qty'];
        });

        $discountAmount = $this->discountAmount($data['discount_code'] ?? null, $subTotal);
        $shippingFee = empty($data['feeship_id']) ? 0 : $this->orders->feeShip((int) $data['feeship_id'])->price;

        return [
            'sub_total' => $subTotal,
            'discount_amount' => $discountAmount,
            'shipping_fee' => $shippingFee,
            'payment_total' => max(0, $subTotal - $discountAmount + $shippingFee),
        ];
    }

    private function discountAmount(?string $discountCode, int $subTotal): int
    {
        $discount = $this->orders->discountByCode($discountCode);

        if (! $discount) {
            return 0;
        }

        if ($discount->starts_at && $discount->starts_at->isFuture()) {
            return 0;
        }

        if ($discount->expires_at && $discount->expires_at->isPast()) {
            return 0;
        }

        if ((int) $discount->is_fixed === 1) {
            return min($subTotal, (int) $discount->discount_amount);
        }

        return min($subTotal, (int) floor($subTotal * ((int) $discount->discount_amount / 100)));
    }

    private function format(Order $order): array
    {
        return [
            'id' => $order->id,
            'staff_id' => $order->staff_id,
            'customer_id' => $order->customer_id,
            'customer_name' => $order->customer?->name,
            'customer_email' => $order->customer?->email,
            'customer' => $order->customer ? [
                'id' => $order->customer->id,
                'name' => $order->customer->name,
                'email' => $order->customer->email,
            ] : null,
            'staff' => $order->staff ? [
                'id' => $order->staff->id,
                'name' => $order->staff->name,
                'email' => $order->staff->email,
            ] : null,
            'shipping_fullname' => $order->shipping_fullname,
            'shipping_mobile' => $order->shipping_mobile,
            'payment_method' => (int) $order->payment_method,
            'payment_method_label' => Order::PAYMENT_METHODS[(int) $order->payment_method] ?? '',
            'shipping_ward_id' => $order->shipping_ward_id,
            'shipping_housenumber_street' => $order->shipping_housenumber_street,
            'shipping_fee' => (int) $order->shipping_fee,
            'feeship_id' => $order->feeship_id,
            'delivered_date' => optional($order->delivered_date)->toDateString(),
            'price_total' => (int) $order->price_total,
            'discount_code' => $order->discount_code ?: null,
            'discount_amount' => (int) $order->discount_amount,
            'sub_total' => (int) $order->sub_total,
            'tax' => (int) $order->tax,
            'price_inc_tax_total' => (int) $order->price_inc_tax_total,
            'voucher_code' => $order->voucher_code,
            'voucher_amount' => (int) $order->voucher_amount,
            'payment_total' => (int) $order->payment_total,
            'status' => $order->status,
            'note' => $order->note,
            'items' => $order->items->map(fn (OrderItem $item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ])->values()->all(),
            'created_at' => optional($order->created_at)->toDateTimeString(),
            'updated_at' => optional($order->updated_at)->toDateTimeString(),
        ];
    }
}
