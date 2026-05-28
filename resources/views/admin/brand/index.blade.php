@extends('admin.layout.app')
@section('title', 'Brands')
@section('content')
    @php
        $brandRows = collect($brands ?? [])->map(function ($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'status' => (int) ($brand->status ?? 0),
                'created_at' => optional($brand->created_at)->format('Y-m-d H:i:s') ?? (string) $brand->created_at,
                'updated_at' => optional($brand->updated_at)->format('Y-m-d H:i:s') ?? (string) $brand->updated_at,
                'logoUrl' => $brand->getFirstMediaUrl('brands'),
                'logoAlt' => $brand->name,
                'editUrl' => route('admin.brand.edit', ['id' => $brand->id]),
                'deleteUrl' => route('admin.brand.destroy', ['brand' => $brand->id]),
                'changeStatusUrl' => route('admin.brand.change_status', ['brand' => $brand->id]),
            ];
        })->values();
    @endphp
    <div
        data-react-component="AdminResourceTable"
        data-props='@json([
            "breadcrumbs" => [
                ["href" => route("admin.dashboard"), "label" => __("translate.management")],
                ["active" => true, "label" => __("translate.brands")],
            ],
            "actions" => [
                ["href" => route("admin.brand.create"), "label" => __("translate.add")],
                ["type" => "submit", "name" => "delete", "label" => __("translate.delete")],
            ],
            "rows" => $brandRows,
            "resourceName" => "brands",
            "csrfToken" => csrf_token(),
            "showLogo" => true,
            "showListAction" => false,
            "labels" => [
                "logo" => __("translate.logo"),
                "name" => __("translate.name"),
                "status" => __("translate.status"),
                "createdAt" => __("translate.createdAt"),
                "updatedAt" => __("translate.updatedAt"),
                "management" => __("translate.management"),
                "active" => __("translate.active"),
                "inactive" => __("translate.inactive"),
                "items" => __("translate.brands"),
                "searchPlaceholder" => "Tìm thương hiệu...",
                "allStatus" => "Tất cả trạng thái",
                "selected" => "đã chọn",
                "emptyMessage" => "Không tìm thấy thương hiệu phù hợp.",
            ],
        ])'
    ></div>
@endsection
@push('scripts')
    <script>
        window.translations = {
            confirmDelete: @json(__('translate.confirmDelete')),
            deleteButton: @json(__('translate.buttonDelete')),
            cancelButton: @json(__('translate.buttonCancel')),
            confirmButton: @json(__('translate.confirmButton')),
            confirmChangeStatus: @json(__('translate.confirmChangeStatus')),
        };
    </script>
    <script type="module" src="{{ asset('') }}/adm/js/brand/index.js"></script>
@endpush
