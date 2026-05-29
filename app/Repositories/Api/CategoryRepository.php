<?php

namespace App\Repositories\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryRepository
{
    public function all(Request $request): Collection
    {
        if (! $request->filled('q')) {
            $query = Category::query()->latest();
            $this->applyFilters($query, $request);

            return $query->get();
        }

        $search = Category::search((string) $request->input('q'));

        if ($request->filled('parent_id')) {
            $search->where('parent_id', $request->integer('parent_id'));
        }

        if ($request->filled('status')) {
            $search->where('status', $request->integer('status'));
        }

        return $search->query(function ($query) use ($request) {
            $query->latest()
                ->where('name', 'like', '%' . $request->input('q') . '%');

            $this->applyFilters($query, $request);
        })->get();
    }

    public function find(int|string $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->boolean('parents_only')) {
            $query->whereNull('parent_id');
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->integer('parent_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->integer('status'));
        }
    }
}
