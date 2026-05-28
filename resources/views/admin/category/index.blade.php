@extends('admin.layout.app')
@section('title', 'Categories')
@section('content')
    @php
        $categoryRows = collect($categories ?? [])->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'status' => (int) ($category->status ?? 0),
                'created_at' => optional($category->created_at)->format('Y-m-d H:i:s') ?? (string) $category->created_at,
                'updated_at' => optional($category->updated_at)->format('Y-m-d H:i:s') ?? (string) $category->updated_at,
                'listUrl' => route('admin.category.list', ['id' => $category->id]),
                'editUrl' => route('admin.category.edit', ['id' => $category->id]),
                'deleteUrl' => route('admin.category.destroy', ['category' => $category->id]),
                'changeStatusUrl' => route('admin.category.change_status', ['category' => $category->id]),
            ];
        })->values();
    @endphp
    <div
        data-react-component="AdminResourceTable"
        data-props='@json([
            "breadcrumbs" => [
                ["href" => route("admin.dashboard"), "label" => __("translate.management")],
                ["active" => true, "label" => __("translate.categories")],
            ],
            "actions" => [
                ["href" => route("admin.category.create"), "label" => __("translate.add")],
                ["type" => "submit", "name" => "delete", "label" => __("translate.delete")],
            ],
            "rows" => $categoryRows,
            "resourceName" => "categories",
            "csrfToken" => csrf_token(),
            "showListAction" => true,
            "labels" => [
                "name" => __("translate.name"),
                "status" => __("translate.status"),
                "createdAt" => __("translate.createdAt"),
                "updatedAt" => __("translate.updatedAt"),
                "management" => __("translate.management"),
                "active" => __("translate.active"),
                "inactive" => __("translate.inactive"),
                "items" => __("translate.categories"),
                "searchPlaceholder" => "Tìm danh mục...",
                "allStatus" => "Tất cả trạng thái",
                "selected" => "đã chọn",
                "emptyMessage" => "Không tìm thấy danh mục phù hợp.",
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
    <script type="module" src="{{ asset('') }}/adm/js/category/index.js"></script>
@endpush
