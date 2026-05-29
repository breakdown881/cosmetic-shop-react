<?php

namespace App\Services\Customer;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Customer\CustomerChatbotRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomerChatbotService
{
    public function __construct(private readonly CustomerChatbotRepository $chatbotRepository) {}

    public function answer(?User $user, string $message): array
    {
        $normalized = $this->normalize($message);
        $suggestions = $this->productSuggestions($normalized);

        if ($suggestions !== []) {
            $intent = 'product_recommendation';
            $answer = 'Minh tim thay mot vai san pham phu hop cho ban: '
                . collect($suggestions)->pluck('name')->implode(', ')
                . '.';
        } elseif ($this->containsAny($normalized, ['ship', 'giao hang', 'van chuyen', 'phi ship'])) {
            $intent = 'shipping_policy';
            $answer = 'Goda Shop ho tro giao hang toan quoc. Phi ship se duoc tinh o buoc thanh toan theo khu vuc nhan hang.';
        } elseif ($this->containsAny($normalized, ['doi tra', 'hoan tien', 'tra hang'])) {
            $intent = 'return_policy';
            $answer = 'Ban co the lien he shop de duoc ho tro doi tra neu san pham loi, giao sai hoac con nguyen dieu kien doi tra.';
        } elseif ($this->containsAny($normalized, ['thanh toan', 'cod', 'chuyen khoan'])) {
            $intent = 'payment_policy';
            $answer = 'Shop hien ho tro thanh toan COD va chuyen khoan ngan hang neu don hang du dieu kien.';
        } else {
            $intent = 'fallback';
            $answer = 'Minh chua co du thong tin cho cau hoi nay. Ban co the hoi ve san pham, phi ship, thanh toan hoac doi tra nhe.';
        }

        $this->chatbotRepository->logMessage($user?->id, $message, $answer, $intent, $suggestions);

        return [
            'reply' => $answer,
            'intent' => $intent,
            'suggestions' => $suggestions,
        ];
    }

    private function productSuggestions(string $normalizedMessage): array
    {
        $terms = collect(explode(' ', $normalizedMessage))
            ->map(fn (string $term) => trim($term))
            ->filter(fn (string $term) => mb_strlen($term) >= 3)
            ->reject(fn (string $term) => in_array($term, ['toi', 'can', 'cho', 'cua', 'ban', 'minh', 'san', 'pham'], true))
            ->values();

        if ($terms->isEmpty()) {
            return [];
        }

        return $this->chatbotRepository->activeProducts()
            ->map(fn (Product $product) => [
                'product' => $product,
                'score' => $this->scoreProduct($product, $terms),
            ])
            ->filter(fn (array $row) => $row['score'] > 0)
            ->sortByDesc('score')
            ->take(3)
            ->map(fn (array $row) => $this->formatProduct($row['product']))
            ->values()
            ->all();
    }

    private function scoreProduct(Product $product, Collection $terms): int
    {
        $haystack = $this->normalize($product->name . ' ' . $product->description);

        return $terms->sum(fn (string $term) => Str::contains($haystack, $term) ? 1 : 0);
    }

    private function formatProduct(Product $product): array
    {
        $price = (int) $product->price;
        $discountPercentage = max(0, min(100, (int) $product->discount_percentage));
        $salePrice = $discountPercentage > 0
            ? (int) round($price * (100 - $discountPercentage) / 100)
            : $price;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $salePrice,
            'url' => '/products/' . $product->id,
        ];
    }

    private function containsAny(string $message, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (Str::contains($message, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $value): string
    {
        $value = Str::ascii(Str::lower($value));
        $value = preg_replace('/[^a-z0-9\s]+/', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
