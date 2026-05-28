@extends('admin.layout.app')
@section('title', 'Categories')
@section('content')
    @php
        if (empty($isChild)) {
            $url = route('admin.category.store');
            $urlBack = route('admin.category.index');
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.category.index'), 'label' => __('translate.categories')],
                ['active' => true, 'label' => __('translate.add')],
            ];
        } else {
            $url = route('admin.category.store.child', ['id' => $category->id]);
            $urlBack = route('admin.category.list', ['id' => $category->id]);
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.category.index'), 'label' => __('translate.categories')],
                ['href' => route('admin.category.list', ['id' => $category->id]), 'label' => $category->name],
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
