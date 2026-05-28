@extends('admin.layout.app')
@section('title', 'Categories')
@section('content')
    @php
        $categoryRows = collect($categories ?? [])->map(function ($child) use ($category) {
            return [
                'id' => $child->id,
                'name' => $child->name,
                'status' => (int) ($child->status ?? 0),
                'created_at' => optional($child->created_at)->format('Y-m-d H:i:s') ?? (string) $child->created_at,
                'updated_at' => optional($child->updated_at)->format('Y-m-d H:i:s') ?? (string) $child->updated_at,
                'editUrl' => route('admin.category.edit.child', ['id' => $category->id, 'category' => $child->id]),
                'deleteUrl' => route('admin.category.destroy', ['category' => $child->id]),
                'changeStatusUrl' => route('admin.category.change_status', ['category' => $child->id]),
            ];
        })->values();
    @endphp
    <div
        data-react-component="AdminResourceTable"
        data-props='@json([
            "breadcrumbs" => [
                ["href" => route("admin.dashboard"), "label" => __("translate.management")],
                ["href" => route("admin.category.index"), "label" => __("translate.categories")],
                ["active" => true, "label" => $category->name],
            ],
            "actions" => [
                ["href" => route("admin.category.create.child", ["id" => $category->id]), "label" => __("translate.add")],
                ["type" => "submit", "name" => "delete", "label" => __("translate.delete")],
            ],
            "rows" => $categoryRows,
            "resourceName" => "categories",
            "csrfToken" => csrf_token(),
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
