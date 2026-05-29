<?php

namespace App\Services\Customer;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Customer\CustomerReviewRepository;
use Illuminate\Support\Collection;

class CustomerReviewService
{
    public function __construct(private readonly CustomerReviewRepository $reviews) {}

    public function approvedForProduct(Product $product): Collection
    {
        return $this->reviews->approvedReviews($product->id);
    }

    public function reviewState(Product $product, ?User $user): array
    {
        if (! $user) {
            return [
                'canReview' => false,
                'hasReviewed' => false,
            ];
        }

        $hasReviewed = $this->reviews->hasReviewed($user, $product);

        return [
            'canReview' => $this->reviews->userPurchasedProduct($user, $product) && ! $hasReviewed,
            'hasReviewed' => $hasReviewed,
        ];
    }

    public function store(User $user, int|string $productId, array $data): Product
    {
        $product = $this->reviews->findActiveProduct($productId);

        if (! $this->reviews->userPurchasedProduct($user, $product)) {
            abort(403, 'Only customers who purchased this product can review it.');
        }

        if ($this->reviews->hasReviewed($user, $product)) {
            abort(422, 'You already reviewed this product.');
        }

        $this->reviews->createPending($user, $product, $data);

        return $product;
    }
}
