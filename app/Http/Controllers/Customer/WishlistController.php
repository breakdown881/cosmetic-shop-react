<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreWishlistItemRequest;
use App\Services\Customer\CustomerWishlistService;
use App\Support\PublicReactShell;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WishlistController extends Controller
{
    public function __construct(
        private readonly CustomerWishlistService $wishlist,
        private readonly PublicReactShell $shell,
    ) {}

    public function index(Request $request): Response
    {
        $props = $this->wishlist->props($request->user());

        return $this->shell->render('CustomerWishlistPage', $props, $props['title']);
    }

    public function store(StoreWishlistItemRequest $request): RedirectResponse
    {
        $this->wishlist->add($request->user(), (int) $request->validated('product_id'));

        return redirect('/wishlist');
    }

    public function destroy(Request $request, string $product): RedirectResponse
    {
        $this->wishlist->remove($request->user(), (int) $product);

        return redirect('/wishlist');
    }
}
