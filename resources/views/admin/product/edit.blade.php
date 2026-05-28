@extends('admin.layout.app')
@section('title', 'Products')
@section('content')
    @php
        if (empty($isChild)) {
            $url = route('admin.product.update', ['product' => $product->id]);
            $urlBack = route('admin.product.index');
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.product.index'), 'label' => __('translate.products')],
                ['active' => true, 'label' => $product->name],
            ];
        } else {
            $url = route('admin.product.update.child', ['id' => $parent->id, 'product' => $product->id]);
            $urlBack = route('admin.product.list', ['id' => $parent->id]);
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.product.index'), 'label' => __('translate.products')],
                ['href' => route('admin.product.list', ['id' => $parent->id]), 'label' => $parent->name],
                ['active' => true, 'label' => $product->name],
            ];
        }
    @endphp
    <div
        data-react-component="AdminResourceForm"
        data-props='@json([
            "breadcrumbs" => $breadcrumbs,
            "action" => $url,
            "backUrl" => $urlBack,
            "csrfToken" => csrf_token(),
            "method" => "PATCH",
            "fields" => [
                ["label" => __("translate.name"), "name" => "name", "type" => "text", "value" => old("name", $product->name), "required" => true, "maxLength" => 255],
                [
                    "label" => __("translate.status"), "name" => "status", "type" => "select", "value" => old("status", (int) ($product->status ?? 0)), "required" => true,
                    "options" => [["value" => 0, "label" => __("translate.inactive")], ["value" => 1, "label" => __("translate.active")]],
                ],
            ],
            "labels" => ["save" => __("translate.save"), "back" => __("translate.back")],
        ])'
    ></div>
@endsection
