@extends('admin.layout.app')
@section('title', 'Categories')
@section('content')
    @php
        if (empty($isChild)) {
            $url = route('admin.category.update', ['category' => $category->id]);
            $urlBack = route('admin.category.index');
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.category.index'), 'label' => __('translate.categories')],
                ['active' => true, 'label' => $category->name],
            ];
        } else {
            $url = route('admin.category.update.child', ['id' => $parent->id, 'category' => $category->id]);
            $urlBack = route('admin.category.list', ['id' => $parent->id]);
            $breadcrumbs = [
                ['href' => route('admin.dashboard'), 'label' => __('translate.management')],
                ['href' => route('admin.category.index'), 'label' => __('translate.categories')],
                ['href' => route('admin.category.list', ['id' => $parent->id]), 'label' => $parent->name],
                ['active' => true, 'label' => $category->name],
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
                ["label" => __("translate.name"), "name" => "name", "type" => "text", "value" => old("name", $category->name), "required" => true, "maxLength" => 255],
                [
                    "label" => __("translate.status"), "name" => "status", "type" => "select", "value" => old("status", (int) $category->status), "required" => true,
                    "options" => [["value" => 0, "label" => __("translate.inactive")], ["value" => 1, "label" => __("translate.active")]],
                ],
            ],
            "labels" => ["save" => __("translate.save"), "back" => __("translate.back")],
        ])'
    ></div>
@endsection
