@extends('admin.layout.app')
@section('title', 'Brands')
@section('content')
    <div
        data-react-component="AdminResourceForm"
        data-props='@json([
            "breadcrumbs" => [
                ["href" => route("admin.dashboard"), "label" => __("translate.management")],
                ["href" => route("admin.brand.index"), "label" => __("translate.brands")],
                ["active" => true, "label" => __("translate.add")],
            ],
            "action" => route("admin.brand.store"),
            "backUrl" => route("admin.brand.index"),
            "csrfToken" => csrf_token(),
            "method" => "POST",
            "fields" => [
                ["label" => __("translate.name"), "name" => "name", "type" => "text", "value" => old("name", ""), "required" => true, "maxLength" => 255],
                ["label" => __("translate.image.title"), "name" => "image", "type" => "file", "accept" => ".jpg,.jpeg,.png"],
                [
                    "label" => __("translate.status"), "name" => "status", "type" => "select", "value" => old("status", 0), "required" => true,
                    "options" => [["value" => 0, "label" => __("translate.inactive")], ["value" => 1, "label" => __("translate.active")]],
                ],
            ],
            "labels" => ["save" => __("translate.save"), "back" => __("translate.back"), "preview" => "Xem trước"],
        ])'
    ></div>
@endsection
