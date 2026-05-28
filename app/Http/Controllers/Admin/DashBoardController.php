<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::query()
            ->with('customer:id,name,email')
            ->latest()
            ->limit(10)
            ->get();
        $cancelledOrders = Order::query()->where('status', 'CANCELLED')->count();
        $revenue = Order::query()
            ->where('status', '!=', 'CANCELLED')
            ->sum('payment_total');

        return \App\Support\AdminReactShell::render('AdminDashboard', [
                'periods' => [
                    ['key' => 'today', 'label' => __('translate.today')],
                    ['key' => 'yesterday', 'label' => __('translate.yesterday')],
                    ['key' => 'thisWeek', 'label' => __('translate.thisWeek')],
                    ['key' => 'thisMonth', 'label' => __('translate.thisMonth')],
                    ['key' => 'threeMonths', 'label' => __('translate.3month')],
                    ['key' => 'thisYear', 'label' => __('translate.thisYear')],
                ],
                'metrics' => [
                    ['key' => 'orders', 'label' => __('translate.orders'), 'value' => Order::query()->count()],
                    ['key' => 'revenue', 'label' => __('translate.revenue'), 'value' => (int) $revenue, 'type' => 'currency'],
                    ['key' => 'cancelledOrders', 'label' => __('translate.cancelledOrders'), 'value' => $cancelledOrders],
                ],
                'orders' => $orders->map(fn (Order $order) => [
                    'id' => $order->id,
                    'customerName' => $order->customer?->name ?? $order->shipping_fullname,
                    'customerPhone' => $order->shipping_mobile,
                    'status' => $order->status,
                    'orderDate' => optional($order->created_at)->toDateTimeString(),
                    'paymentMethod' => (int) $order->payment_method === 0 ? 'COD' : 'Bank',
                    'total' => (int) $order->payment_total,
                    'deliveryAddress' => $order->shipping_housenumber_street,
                ])->values(),
                'labels' => [
                    'code' => __('translate.code'),
                    'confirm' => __('translate.confirm'),
                    'customerName' => __('translate.customerName'),
                    'customerPhone' => __('translate.customerPhone'),
                    'delete' => __('translate.delete'),
                    'deliveryAddress' => __('translate.deliveryAddress'),
                    'detail' => __('translate.detail'),
                    'edit' => __('translate.edit'),
                    'emptyOrders' => 'Chưa có đơn hàng.',
                    'find' => __('translate.find'),
                    'fromDate' => __('translate.fromDate'),
                    'orderDate' => __('translate.orderDate'),
                    'orders' => __('translate.orders'),
                    'paymentMethod' => __('translate.paymentMethod'),
                    'status' => __('translate.status'),
                    'toDate' => __('translate.toDate'),
                    'total' => __('translate.total'),
                ],
        ], 'dashboard', __('translate.overview'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
