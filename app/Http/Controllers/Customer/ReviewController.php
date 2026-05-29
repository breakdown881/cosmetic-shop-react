<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreProductReviewRequest;
use App\Services\Customer\CustomerReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(private readonly CustomerReviewService $reviews) {}

    public function store(StoreProductReviewRequest $request, string $product): RedirectResponse
    {
        $reviewedProduct = $this->reviews->store($request->user(), $product, $request->validated());

        return redirect('/products/' . $reviewedProduct->id);
    }
}
