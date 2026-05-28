@extends('admin.layout.app')
@section('title', 'Products')
@section('content')
    @php
        $productRows = collect($products ?? $categories ?? [])->map(function ($child) use ($product) {
            return [
                'id' => $child->id,
                'name' => $child->name,
                'status' => (int) ($child->status ?? 0),
                'created_at' => optional($child->created_at)->format('Y-m-d H:i:s') ?? (string) $child->created_at,
                'updated_at' => optional($child->updated_at)->format('Y-m-d H:i:s') ?? (string) $child->updated_at,
                'editUrl' => route('admin.product.edit.child', ['id' => $product->id, 'product' => $child->id]),
                'deleteUrl' => route('admin.product.destroy', ['product' => $child->id]),
                'changeStatusUrl' => route('admin.product.change_status', ['product' => $child->id]),
            ];
        })->values();
    @endphp
    <div
        data-react-component="AdminResourceTable"
        data-props='@json([
            "breadcrumbs" => [
                ["href" => route("admin.dashboard"), "label" => __("translate.management")],
                ["href" => route("admin.product.index"), "label" => __("translate.products")],
                ["active" => true, "label" => $product->name],
            ],
            "actions" => [
                ["href" => route("admin.product.create.child", ["id" => $product->id]), "label" => __("translate.add")],
                ["type" => "submit", "name" => "delete", "label" => __("translate.delete")],
            ],
            "rows" => $productRows,
            "resourceName" => "products",
            "csrfToken" => csrf_token(),
            "labels" => [
                "name" => __("translate.name"),
                "status" => __("translate.status"),
                "createdAt" => __("translate.createdAt"),
                "updatedAt" => __("translate.updatedAt"),
                "management" => __("translate.management"),
                "active" => __("translate.active"),
                "inactive" => __("translate.inactive"),
                "items" => __("translate.products"),
                "searchPlaceholder" => "Tìm sản phẩm...",
                "allStatus" => "Tất cả trạng thái",
                "selected" => "đã chọn",
                "emptyMessage" => "Không tìm thấy sản phẩm phù hợp.",
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
    <script type="module" src="{{ asset('') }}/adm/js/product/index.js"></script>
@endpush
