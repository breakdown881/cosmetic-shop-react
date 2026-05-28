@extends('admin.layout.app')
@section('title', 'Products')
@section('content')
    @php
        if (empty($isChild)) {
            $url = route('admin.product.store');
            $urlBack = route('admin.product.index');
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.product.index'), 'label' => __('translate.products')],
                ['active' => true, 'label' => __('translate.add')],
            ];
        } else {
            $url = route('admin.product.store.child', ['id' => $product->id]);
            $urlBack = route('admin.product.list', ['id' => $product->id]);
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.product.index'), 'label' => __('translate.products')],
                ['href' => route('admin.product.list', ['id' => $product->id]), 'label' => $product->name],
                ['active' => true, 'label' => __('translate.add')],
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
            "method" => "POST",
            "fields" => [
                ["label" => __("translate.name"), "name" => "name", "type" => "text", "value" => old("name", ""), "required" => true, "maxLength" => 255],
                [
                    "label" => __("translate.status"), "name" => "status", "type" => "select", "value" => old("status", 0), "required" => true,
                    "options" => [["value" => 0, "label" => __("translate.inactive")], ["value" => 1, "label" => __("translate.active")]],
                ],
            ],
            "labels" => ["save" => __("translate.save"), "back" => __("translate.back")],
        ])'
    ></div>
@endsection
