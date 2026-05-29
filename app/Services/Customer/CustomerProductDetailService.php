<?php

namespace App\Services\Customer;

use App\Models\Product;
use App\Repositories\Customer\CustomerProductDetailRepository;
use Illuminate\Support\Facades\Auth;

class CustomerProductDetailService
{
    public function __construct(
        private readonly CustomerProductDetailRepository $detailRepository,
        private readonly CustomerNavigationService $navigationService,
        private readonly CustomerReviewService $reviewService,
        private readonly CustomerWishlistService $wishlistService,
    ) {}

    public function showProps(int|string $productId): array
    {
        $product = $this->detailRepository->findActive($productId);
        $relatedProducts = $this->detailRepository->related($product);
        $mediaUrls = $this->detailRepository->mediaUrls(
            collect([$product])
                ->merge($relatedProducts)
                ->pluck('media_id')
                ->filter()
                ->unique()
                ->values()
                ->all()
        );

        return [
            'title' => $product->name,
            'navItems' => $this->navigationService->navItems(),
            'csrfToken' => csrf_token(),
            'product' => $this->formatProduct($product, $mediaUrls),
            'relatedProducts' => $relatedProducts
                ->map(fn (Product $relatedProduct) => $this->formatProduct($relatedProduct, $mediaUrls))
                ->values(),
        ];
    }

    private function formatProduct(Product $product, array $mediaUrls): array
    {
        $user = Auth::user();
        $price = (int) $product->price;
        $discountPercentage = max(0, min(100, (int) $product->discount_percentage));
        $salePrice = $discountPercentage > 0
            ? (int) round($price * (100 - $discountPercentage) / 100)
            : $price;
        $reviews = $this->reviewService->approvedForProduct($product);
        $reviewState = $this->reviewService->reviewState($product, $user);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'brand_id' => $product->brand_id,
            'brand_name' => $product->brand?->name,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'price' => $price,
            'sale_price' => $salePrice,
            'discount_percentage' => $discountPercentage,
            'inventory_qty' => (int) $product->inventory_qty,
            'description' => $product->description,
            'star' => $product->star,
            'featured' => (bool) $product->featured,
            'featured_image' => $mediaUrls[$product->media_id] ?? asset('adm/images/godakeben450x170.jpg'),
            'url' => '/products/' . $product->id,
            'reviewSummary' => [
                'average' => $reviews->isEmpty() ? 0 : round((float) $reviews->avg('star'), 1),
                'count' => $reviews->count(),
            ],
            'reviews' => $reviews->map(fn ($review) => [
                'id' => $review->id,
                'fullname' => $review->fullname,
                'star' => (float) $review->star,
                'description' => $review->description,
                'created_at' => $review->created_at?->toDateString(),
            ])->values(),
            'canReview' => $reviewState['canReview'],
            'hasReviewed' => $reviewState['hasReviewed'],
            'reviewStoreUrl' => '/products/' . $product->id . '/reviews',
            'wishlist' => $this->wishlistService->state($user, $product),
        ];
    }
}
