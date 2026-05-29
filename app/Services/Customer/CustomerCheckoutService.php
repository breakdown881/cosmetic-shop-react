<?php

namespace App\Services\Customer;

use App\Models\CustomerCheckoutRequest;
use App\Models\Discount;
use App\Jobs\ProcessCustomerOrderJob;
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

        $orderData = [
            'staff_id' => 1,
            'customer_id' => $customer->id,
            'shipping_fullname' => $data['shipping_fullname'],
            'shipping_mobile' => $data['shipping_mobile'],
            'payment_method' => (int) $data['payment_method'],
            'payment_gateway' => $this->gatewayForPaymentMethod((int) $data['payment_method']),
            'payment_status' => $this->paymentStatusForPaymentMethod((int) $data['payment_method']),
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
        ];
        $items = collect($cart['items'])->map(fn ($item) => [
            'product_id' => $item['product_id'],
            'qty' => $item['quantity'],
            'unit_price' => $item['sale_price'],
            'total_price' => $item['subtotal'],
        ])->all();

        $checkoutRequest = $this->checkoutRepository->createCheckoutRequest($customer->id, $orderData, $items);
        ProcessCustomerOrderJob::dispatch($checkoutRequest->id);

        $this->cartService->clear($session);

        return $this->formatCheckoutRequest($checkoutRequest->refresh());
    }

    public function checkoutRequest(int|string $requestId): array
    {
        $customer = Auth::user();
        abort_unless($customer, 401);

        return $this->formatCheckoutRequest(
            $this->checkoutRepository->findCheckoutRequestForCustomer($requestId, $customer->id)
        );
    }

    private function gatewayForPaymentMethod(int $paymentMethod): ?string
    {
        return match ($paymentMethod) {
            2 => 'vnpay',
            3 => 'momo',
            default => null,
        };
    }

    private function paymentStatusForPaymentMethod(int $paymentMethod): string
    {
        return in_array($paymentMethod, [2, 3], true) ? 'PENDING' : 'UNPAID';
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

    private function formatCheckoutRequest(CustomerCheckoutRequest $checkoutRequest): array
    {
        return [
            'id' => $checkoutRequest->id,
            'status' => $checkoutRequest->status,
            'message' => $checkoutRequest->status === CustomerCheckoutRequest::STATUS_QUEUED
                ? 'Your order is queued and will be processed shortly.'
                : null,
            'status_url' => '/checkout/requests/' . $checkoutRequest->id,
            'order' => $checkoutRequest->order ? $this->formatOrder($checkoutRequest->order, $checkoutRequest->payment_payload) : null,
            'payment' => $checkoutRequest->payment_payload,
            'error_message' => $checkoutRequest->error_message,
        ];
    }

    private function formatOrder($order, ?array $payment = null): array
    {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'customer_id' => $order->customer_id,
            'shipping_fullname' => $order->shipping_fullname,
            'shipping_mobile' => $order->shipping_mobile,
            'payment_method' => (int) $order->payment_method,
            'payment_gateway' => $order->payment_gateway,
            'payment_status' => $order->payment_status,
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
            'payment' => $payment,
        ];
    }
}
