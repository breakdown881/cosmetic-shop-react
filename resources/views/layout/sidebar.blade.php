@php
    $sidebarCategories = collect($categories ?? [])->map(function ($category) {
        $firstSlug = \Str::slug($category->name);
        $slug = "{$firstSlug}-{$category->id}";

        return [
            'id' => $category->id,
            'name' => $category->name,
            'url' => route('category.show', ['slug' => $slug]),
        ];
    })->values();

    $priceRange = request()->has('price-range') ? request()->input('price-range') : null;
@endphp

<div
    data-react-component="PublicSidebar"
    data-props='@json([
        "allProductsUrl" => route("product.index"),
        "activeCategoryId" => $catId ?? null,
        "categories" => $sidebarCategories,
        "currentPriceRange" => $priceRange,
        "labels" => [
            "categoriesTitle" => "Danh mục sản phẩm",
            "allProducts" => "Tất cả sản phẩm",
            "priceRangeTitle" => "Khoảng giá",
        ],
    ])'
></div>
