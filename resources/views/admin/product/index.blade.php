@extends('admin.layout.app')
@section('title', 'Products')
@section('content')
    @php
        $productRows = collect($products ?? [])->map(function ($product) {
            return [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => $product->price,
                'inventory_qty' => $product->inventory_qty,
                'featured' => (int) ($product->featured ?? 0),
                'status' => (int) ($product->status ?? $product->featured ?? 0),
                'created_at' => optional($product->created_at)->format('Y-m-d H:i:s') ?? (string) $product->created_at,
                'updated_at' => optional($product->updated_at)->format('Y-m-d H:i:s') ?? (string) $product->updated_at,
                'editUrl' => route('admin.product.edit', ['id' => $product->id]),
                'listUrl' => route('admin.product.comments.index', ['product' => $product->id]),
                'deleteUrl' => route('admin.product.destroy', ['product' => $product->id]),
                'changeStatusUrl' => route('admin.product.change_status', ['product' => $product->id]),
            ];
        })->values();
    @endphp
    <div
        data-react-component="AdminResourceTable"
        data-props='@json([
            "breadcrumbs" => [
                ["href" => route("admin.dashboard"), "label" => __("translate.management")],
                ["active" => true, "label" => __("translate.products")],
            ],
            "actions" => [
                ["href" => route("admin.product.create"), "label" => __("translate.add")],
                ["type" => "submit", "name" => "delete", "label" => __("translate.delete")],
            ],
            "rows" => $productRows,
            "resourceName" => "products",
            "csrfToken" => csrf_token(),
            "showListAction" => true,
            "columns" => [
                ["key" => "code", "label" => "Mã", "width" => 100],
                ["key" => "price", "label" => "Giá", "width" => 120, "type" => "currency"],
                ["key" => "inventory_qty", "label" => "Tồn kho", "width" => 90],
                ["key" => "featured", "label" => "Nổi bật", "width" => 90, "type" => "boolean", "trueLabel" => "Có", "falseLabel" => "Không"],
            ],
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
