@extends('admin.layout.app')
@section('content')
    @php
        $ordersPayload = collect($orders ?? [])->map(function ($order) {
            $address = trim(implode(', ', array_filter([
                $order?->shipping_housenumber_street,
                $order?->ward?->name,
                $order?->ward?->district?->name,
                $order?->ward?->district?->province?->name,
            ])));

            return [
                'id' => $order?->id,
                'customerName' => $order?->customer?->name,
                'customerPhone' => $order?->customer?->mobile,
                'status' => $order?->status?->description,
                'orderDate' => $order?->created_date,
                'paymentMethod' => $order?->payment_method == 0 ? 'COD' : 'Bank',
                'total' => $order?->payment_total,
                'deliveryAddress' => $address,
            ];
        })->values();

        $revenue = 0;
        $cancelOrder = 0;
        foreach ($orders ?? [] as $order) {
            if (($order->order_status_id ?? null) == 6) {
                $cancelOrder++;
            } else {
                $revenue += $order->payment_total ?? 0;
            }
        }
    @endphp

    <div id="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">@lang('translate.overview')</li>
            </ol>

            <div
                data-react-component="AdminDashboard"
                data-props='@json([
                    "periods" => [
                        ["key" => "today", "label" => __("translate.today")],
                        ["key" => "yesterday", "label" => __("translate.yesterday")],
                        ["key" => "thisWeek", "label" => __("translate.thisWeek")],
                        ["key" => "thisMonth", "label" => __("translate.thisMonth")],
                        ["key" => "threeMonths", "label" => __("translate.3month")],
                        ["key" => "thisYear", "label" => __("translate.thisYear")],
                    ],
                    "metrics" => [
                        ["key" => "orders", "label" => __("translate.orders"), "value" => count($orders ?? [])],
                        ["key" => "revenue", "label" => __("translate.revenue"), "value" => $revenue, "type" => "currency"],
                        ["key" => "cancelledOrders", "label" => __("translate.cancelledOrders"), "value" => $cancelOrder],
                    ],
                    "orders" => $ordersPayload,
                    "labels" => [
                        "code" => __("translate.code"),
                        "confirm" => __("translate.confirm"),
                        "customerName" => __("translate.customerName"),
                        "customerPhone" => __("translate.customerPhone"),
                        "delete" => __("translate.delete"),
                        "deliveryAddress" => __("translate.deliveryAddress"),
                        "detail" => __("translate.detail"),
                        "edit" => __("translate.edit"),
                        "emptyOrders" => "Chưa có đơn hàng.",
                        "find" => __("translate.find"),
                        "fromDate" => __("translate.fromDate"),
                        "orderDate" => __("translate.orderDate"),
                        "orders" => __("translate.orders"),
                        "paymentMethod" => __("translate.paymentMethod"),
                        "status" => __("translate.status"),
                        "toDate" => __("translate.toDate"),
                        "total" => __("translate.total"),
                    ],
                ])'
            ></div>
        </div>
    </div>
@endsection
