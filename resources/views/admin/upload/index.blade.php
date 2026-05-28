@extends('admin.layout.app')
@section('content')
    <div id="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Quản lý</a>
                </li>
                <li class="breadcrumb-item active">Hình ảnh</li>
            </ol>

            <div
                data-react-component="AdminMediaManager"
                data-props='@json([
                    "uploadAction" => "",
                    "csrfToken" => csrf_token(),
                    "items" => [
                        ["id" => 25, "src" => asset("images/suaTamSandrasShowerGel.jpg"), "alt" => "Sữa tắm Sandras Shower Gel"],
                        ["id" => 26, "src" => asset("images/boKemTriMunSakura.jpg"), "alt" => "Bộ kem trị mụn Sakura"],
                    ],
                    "labels" => [
                        "delete" => "Xóa",
                        "image" => "Hình ảnh",
                        "management" => "",
                        "upload" => "Upload",
                        "uploadImage" => "Upload hình",
                        "preview" => "Xem trước",
                        "empty" => "Chưa có hình ảnh.",
                    ],
                ])'
            ></div>
        </div>
    </div>
@endsection
