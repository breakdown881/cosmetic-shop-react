<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveOrderRequest;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Order::query()
                ->with(['customer:id,name,email', 'staff:id,name,email', 'items.product:id,name,price'])
                ->latest()
                ->get()
                ->map(fn (Order $order) => $this->formatOrder($order)),
        ]);
    }

    public function options(): JsonResponse
    {
        return response()->json([
            'customers' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
                ->map(fn (User $customer) => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                ])
                ->values(),
            'products' => Product::query()
                ->orderBy('name')
                ->get(['id', 'name', 'price'])
                ->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                ])
                ->values(),
            'discounts' => Discount::query()
                ->orderBy('code')
                ->get(['code', 'is_fixed', 'discount_amount'])
                ->map(fn (Discount $discount) => [
                    'code' => $discount->code,
                    'is_fixed' => (int) $discount->is_fixed,
                    'discount_amount' => (int) $discount->discount_amount,
                ])
                ->values(),
            'feeships' => Transport::query()
                ->with('province:id,name,type')
                ->latest()
                ->get()
                ->map(fn (Transport $feeShip) => [
                    'id' => $feeShip->id,
                    'label' => trim(($feeShip->province?->type ? $feeShip->province->type . ' ' : '') . ($feeShip->province?->name ?? 'Fee ship #' . $feeShip->id)),
                    'price' => $feeShip->price,
                ])
                ->values(),
            'statusOptions' => Order::STATUSES,
            'paymentMethods' => Order::PAYMENT_METHODS,
        ]);
    }

    public function store(SaveOrderRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {
            $order = Order::create($this->orderPayload($request->validated(), $request->user()?->id));
            $this->syncItems($order, $request->validated('items'));

            return $order;
        });

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->formatOrder($this->loadOrder($order)),
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'data' => $this->formatOrder($this->loadOrder($order)),
        ]);
    }

    public function update(SaveOrderRequest $request, Order $order): JsonResponse
    {
        DB::transaction(function () use ($request, $order) {
            $order->update($this->orderPayload($request->validated(), $order->staff_id));
            OrderItem::query()->where('order_id', $order->id)->delete();
            $this->syncItems($order, $request->validated('items'));
        });

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->formatOrder($this->loadOrder($order->refresh())),
        ]);
    }

    public function destroy(Order $order): JsonResponse
    {
        DB::transaction(function () use ($order) {
            OrderItem::query()->where('order_id', $order->id)->delete();
            $order->delete();
        });

        return response()->json(null, 204);
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
            $product = Product::findOrFail($item['product_id']);

            return $product->price * $item['qty'];
        });

        $discountAmount = $this->discountAmount($data['discount_code'] ?? null, $subTotal);
        $shippingFee = empty($data['feeship_id']) ? 0 : Transport::findOrFail($data['feeship_id'])->price;

        return [
            'sub_total' => $subTotal,
            'discount_amount' => $discountAmount,
            'shipping_fee' => $shippingFee,
            'payment_total' => max(0, $subTotal - $discountAmount + $shippingFee),
        ];
    }

    private function discountAmount(?string $discountCode, int $subTotal): int
    {
        if (! $discountCode) {
            return 0;
        }

        $discount = Discount::where('code', $discountCode)->first();

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

    private function syncItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
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

    private function loadOrder(Order $order): Order
    {
        return $order->load(['customer:id,name,email', 'staff:id,name,email', 'items.product:id,name,price']);
    }

    private function formatOrder(Order $order): array
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
