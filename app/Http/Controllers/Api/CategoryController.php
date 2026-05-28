<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->categories($request),
        ]);
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user('admin')?->id ?? $request->user()?->id ?? 1;

        $category = Category::create($data);

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $category,
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => $category,
        ]);
    }

    public function update(CreateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $category,
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'integer', 'in:0,1,2'],
        ]);

        $category->update(['status' => $data['status']]);

        return response()->json([
            'message' => __('translate.changeStatusSuccess'),
            'data' => $category,
        ]);
    }

    private function categories(Request $request)
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
