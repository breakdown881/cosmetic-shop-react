@php
    $cartItems = Cart::content()->map(function ($item, $rowId) {
        return [
            'rowId' => $rowId,
            'name' => $item->name,
            'price' => $item->price,
            'qty' => $item->qty,
            'image' => "../images/{$item->options->image}",
            'url' => '#',
        ];
    })->values();
@endphp

<div data-react-component="Cart" data-items='@json($cartItems)'></div>
