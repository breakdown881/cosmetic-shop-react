<?php

namespace App\Services\Customer;

use App\Models\Discount;
use App\Models\Order;
use App\Repositories\Customer\CustomerCheckoutRepository;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;

class CustomerCheckoutService
{
    public function __construct(
        private readonly CustomerCartService $cartService,
        private readonly CustomerCheckoutRepository $checkoutRepository,
        private readonly CustomerNavigationService $navigationService,
    ) {}

    public function props(Store $session): array
    {
        return [
            'title' => 'Thanh toán',
            'navItems' => $this->navigationService->navItems(),
            'checkout' => [
                'cart' => $this->cartService->payload($session),
                'feeShips' => $this->checkoutRepository->activeFeeShips()
                    ->map(fn ($feeShip) => [
                        'id' => $feeShip->id,
                        'label' => trim(($feeShip->province?->type ? $feeShip->province->type . ' ' : '') . ($feeShip->province?->name ?? 'Fee ship #' . $feeShip->id)),
                        'price' => (int) $feeShip->price,
                    ])
                    ->values(),
                'paymentMethods' => $this->checkoutRepository->paymentMethods(),
            ],
        ];
    }

    public function checkout(Store $session, array $data): array
    {
        $customer = Auth::user();
        abort_unless($customer, 401);

        $cart = $this->cartService->payload($session);
        abort_if(($cart['items'] ?? []) === [], 422, 'Your cart is empty.');

        $shippingFee = (int) ($this->checkoutRepository->feeShip($data['feeship_id'] ?? null)?->price ?? 0);
        $discountAmount = $this->discountAmount($data['discount_code'] ?? null, (int) $cart['total']);
        $paymentTotal = max(0, (int) $cart['total'] - $discountAmount + $shippingFee);

        $order = $this->checkoutRepository->createOrderWithItems([
            'staff_id' => 1,
            'customer_id' => $customer->id,
            'shipping_fullname' => $data['shipping_fullname'],
            'shipping_mobile' => $data['shipping_mobile'],
            'payment_method' => (int) $data['payment_method'],
            'shipping_ward_id' => $data['shipping_ward_id'] ?? '',
            'shipping_housenumber_street' => $data['shipping_housenumber_street'],
            'shipping_fee' => $shippingFee,
            'feeship_id' => $data['feeship_id'] ?? null,
            'delivered_date' => now()->toDateString(),
            'price_total' => (int) $cart['total'],
            'discount_code' => $data['discount_code'] ?? '',
            'discount_amount' => $discountAmount,
            'sub_total' => (int) $cart['total'],
            'tax' => 0,
            'price_inc_tax_total' => (int) $cart['total'],
            'voucher_code' => '',
            'voucher_amount' => 0,
            'payment_total' => $paymentTotal,
            'status' => 'PENDING',
            'note' => $data['note'] ?? null,
        ], collect($cart['items'])->map(fn ($item) => [
            'product_id' => $item['product_id'],
            'qty' => $item['quantity'],
            'unit_price' => $item['sale_price'],
            'total_price' => $item['subtotal'],
        ])->all());

        $this->cartService->clear($session);

        return $this->formatOrder($order);
    }

    private function discountAmount(?string $discountCode, int $subTotal): int
    {
        $discount = $this->checkoutRepository->discountByCode($discountCode);

        if (! $discount || ! $this->isActiveDiscount($discount)) {
            return 0;
        }

        if ((int) $discount->is_fixed === 1) {
            return min($subTotal, (int) $discount->discount_amount);
        }

        return min($subTotal, (int) floor($subTotal * ((int) $discount->discount_amount / 100)));
    }

    private function isActiveDiscount(Discount $discount): bool
    {
        return ! ($discount->starts_at && $discount->starts_at->isFuture())
            && ! ($discount->expires_at && $discount->expires_at->isPast());
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'customer_id' => $order->customer_id,
            'shipping_fullname' => $order->shipping_fullname,
            'shipping_mobile' => $order->shipping_mobile,
            'payment_method' => (int) $order->payment_method,
            'shipping_fee' => (int) $order->shipping_fee,
            'discount_code' => $order->discount_code ?: null,
            'discount_amount' => (int) $order->discount_amount,
            'sub_total' => (int) $order->sub_total,
            'payment_total' => (int) $order->payment_total,
            'items' => $order->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'qty' => (int) $item->qty,
                'unit_price' => (int) $item->unit_price,
                'total_price' => (int) $item->total_price,
            ])->values(),
        ];
    }
}
