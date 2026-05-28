@php
    $prefixSlug = \Str::slug($product->name);
    $slug = "{$prefixSlug}-{$product->id}";
    $productPayload = [
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'sale_price' => $product->sale_price,
        'featured_image' => $product->featured_image,
        'url' => route('product.show', ['slug' => $slug]),
    ];
@endphp

<div
    class="product-container"
    data-react-component="ProductCard"
    data-props='@json(["product" => $productPayload])'
></div>
