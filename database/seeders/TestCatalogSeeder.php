<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class TestCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $brands = collect([
            'Goda Lab',
            'Pure Bloom',
            'Derma Clear',
            'Skin Aura',
            'Velvet Rose',
            'Sun Mist',
            'Herbal Glow',
            'Aqua Belle',
        ])->map(fn (string $name) => Brand::query()->firstOrCreate(
            ['name' => $name],
            ['status' => 1, 'created_by' => 1],
        ))->values();

        $categories = collect([
            'Cleanser',
            'Toner',
            'Serum',
            'Moisturizer',
            'Sunscreen',
            'Face Mask',
            'Body Care',
            'Makeup Base',
            'Acne Care',
            'Whitening Care',
        ])->map(fn (string $name) => Category::query()->firstOrCreate(
            ['name' => $name],
            ['parent_id' => null, 'status' => 1, 'created_by' => 1],
        ))->values();

        $productLines = [
            'Gentle Foam',
            'Hydra Serum',
            'Bright Cream',
            'Daily Sunscreen',
            'Calm Toner',
            'Repair Mask',
            'Acne Gel',
            'Body Lotion',
            'Makeup Primer',
            'Vitamin Essence',
        ];

        Product::withoutSyncingToSearch(function () use ($brands, $categories, $productLines): void {
            for ($i = 1; $i <= 300; $i++) {
                $brand = $brands[($i - 1) % $brands->count()];
                $category = $categories[($i - 1) % $categories->count()];
                $line = $productLines[($i - 1) % count($productLines)];
                $variant = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
                $discount = ($i % 5 === 0) ? 15 : (($i % 3 === 0) ? 10 : 0);

                Product::query()->updateOrCreate(
                    ['code' => 'TEST-' . $variant],
                    [
                        'name' => $brand->name . ' ' . $line . ' Test ' . $variant,
                        'brand_id' => $brand->id,
                        'category_id' => $category->id,
                        'price' => 85000 + (($i % 40) * 10000),
                        'discount_percentage' => $discount,
                        'discount_from_date' => now()->subDay()->toDateString(),
                        'discount_to_date' => now()->addMonths(3)->toDateString(),
                        'media_id' => 1,
                        'inventory_qty' => 5 + ($i % 80),
                        'description' => 'Test cosmetic product for catalog, filtering, cart, and checkout flows.',
                        'star' => 3.5 + (($i % 16) / 10),
                        'featured' => $i % 4 === 0,
                        'created_by' => 1,
                        'status' => 1,
                    ],
                );
            }
        });
    }
}
