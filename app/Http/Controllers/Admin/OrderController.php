<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transport;
use App\Models\User;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index', [
            'title' => 'Orders',
            'currentMenu' => 'orders',
            'componentProps' => [
                'apiUrl' => route('admin.api.orders.index'),
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'title' => __('translate.orders'),
                'breadcrumbs' => [
                    ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                    ['active' => true, 'label' => __('translate.orders')],
                ],
                'customers' => User::query()
                    ->orderBy('name')
                    ->get(['id', 'name', 'email'])
                    ->map(fn (User $customer) => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                    ])
                    ->values()
                    ->all(),
                'products' => Product::query()
                    ->orderBy('name')
                    ->get(['id', 'name', 'price'])
                    ->map(fn (Product $product) => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                    ])
                    ->values()
                    ->all(),
                'discounts' => Discount::query()
                    ->orderBy('code')
                    ->get(['code', 'is_fixed', 'discount_amount'])
                    ->map(fn (Discount $discount) => [
                        'code' => $discount->code,
                        'is_fixed' => (int) $discount->is_fixed,
                        'discount_amount' => (int) $discount->discount_amount,
                    ])
                    ->values()
                    ->all(),
                'feeships' => Transport::query()
                    ->with('province:id,name,type')
                    ->latest()
                    ->get()
                    ->map(fn (Transport $feeShip) => [
                        'id' => $feeShip->id,
                        'label' => trim(($feeShip->province?->type ? $feeShip->province->type . ' ' : '') . ($feeShip->province?->name ?? 'Fee ship #' . $feeShip->id)),
                        'price' => $feeShip->price,
                    ])
                    ->values()
                    ->all(),
                'statusOptions' => Order::STATUSES,
                'paymentMethods' => Order::PAYMENT_METHODS,
            ],
        ]);
    }
}
