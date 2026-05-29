<?php

namespace App\Repositories\Customer;

use App\Models\Discount;
use App\Models\CustomerCheckoutRequest;
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

    public function latestOrderForCustomer(int $customerId): ?Order
    {
        return Order::query()
            ->where('customer_id', $customerId)
            ->latest()
            ->first();
    }

    public function createCheckoutRequest(?int $customerId, array $orderData, array $items): CustomerCheckoutRequest
    {
        return CustomerCheckoutRequest::query()->create([
            'customer_id' => $customerId,
            'status' => CustomerCheckoutRequest::STATUS_QUEUED,
            'order_data' => $orderData,
            'items' => $items,
        ]);
    }

    public function findCheckoutRequestForCustomer(int|string $id, int $customerId): CustomerCheckoutRequest
    {
        return CustomerCheckoutRequest::query()
            ->with(['order.items.product:id,name,price'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);
    }

    public function findGuestCheckoutRequest(int|string $id): CustomerCheckoutRequest
    {
        return CustomerCheckoutRequest::query()
            ->with(['order.items.product:id,name,price'])
            ->whereNull('customer_id')
            ->findOrFail($id);
    }

    public function findCheckoutRequest(int|string $id): CustomerCheckoutRequest
    {
        return CustomerCheckoutRequest::query()->findOrFail($id);
    }

    public function markCheckoutProcessing(CustomerCheckoutRequest $checkoutRequest): CustomerCheckoutRequest
    {
        $checkoutRequest->forceFill([
            'status' => CustomerCheckoutRequest::STATUS_PROCESSING,
            'error_message' => null,
        ])->save();

        return $checkoutRequest->refresh();
    }

    public function markCheckoutCompleted(CustomerCheckoutRequest $checkoutRequest, Order $order, ?array $payment): CustomerCheckoutRequest
    {
        $checkoutRequest->forceFill([
            'order_id' => $order->id,
            'status' => CustomerCheckoutRequest::STATUS_COMPLETED,
            'payment_payload' => $payment,
            'processed_at' => now(),
            'error_message' => null,
        ])->save();

        return $checkoutRequest->refresh()->load(['order.items.product:id,name,price']);
    }

    public function markCheckoutFailed(CustomerCheckoutRequest $checkoutRequest, string $message): CustomerCheckoutRequest
    {
        $checkoutRequest->forceFill([
            'status' => CustomerCheckoutRequest::STATUS_FAILED,
            'error_message' => $message,
            'processed_at' => now(),
        ])->save();

        return $checkoutRequest->refresh();
    }
}
