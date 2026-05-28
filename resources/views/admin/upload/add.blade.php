@extends('admin.layout.app')
@section('content')
    <div id="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Quản lý</a>
                </li>
                <li class="breadcrumb-item active">Sản phẩm</li>
            </ol>

            <div
                data-react-component="AdminResourceForm"
                data-props='@json([
                    "action" => "",
                    "backUrl" => "#",
                    "csrfToken" => csrf_token(),
                    "method" => "POST",
                    "fields" => [
                        ["label" => "Tên", "name" => "name", "type" => "text", "value" => ""],
                        ["label" => "Giá bán lẻ", "name" => "wholesale-price", "type" => "number", "value" => "", "min" => 0],
                        ["label" => "Lượng tồn", "name" => "inventory-number", "type" => "number", "value" => "", "min" => 0],
                        [
                            "label" => "Danh mục",
                            "name" => "category",
                            "type" => "select",
                            "value" => "Kem Chống Nắng",
                            "options" => [
                                ["value" => "Kem Chống Nắng", "label" => "Kem Chống Nắng"],
                                ["value" => "Kem Dưỡng Da", "label" => "Kem Dưỡng Da"],
                                ["value" => "Kem Trị Mụn", "label" => "Kem Trị Mụn"],
                                ["value" => "Kem Trị Thâm Nám", "label" => "Kem Trị Thâm Nám"],
                                ["value" => "Sữa Rửa Mặt", "label" => "Sữa Rửa Mặt"],
                                ["value" => "Sữa Tắm", "label" => "Sữa Tắm"],
                            ],
                        ],
                        ["label" => "Nổi bật", "name" => "featured", "type" => "checkbox", "value" => 1],
                        ["label" => "Hình ảnh", "name" => "image", "type" => "file", "accept" => ".jpg,.jpeg,.png"],
                        ["label" => "Mô tả", "name" => "description", "type" => "textarea", "value" => "", "rows" => 10],
                    ],
                    "labels" => [
                        "save" => "Lưu",
                        "back" => "Quay lại",
                        "preview" => "Xem trước",
                    ],
                ])'
            ></div>
        </div>
    </div>
@endsection
