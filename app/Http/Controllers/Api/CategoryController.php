<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Services\Api\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categories) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->categories->all($request),
        ]);
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->categories->create(
                $request->validated(),
                $request->user('admin')?->id ?? $request->user()?->id
            ),
        ], 201);
    }

    public function show($category): JsonResponse
    {
        return response()->json([
            'data' => $this->categories->find($category),
        ]);
    }

    public function update(CreateCategoryRequest $request, $category): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->categories->update($category, $request->validated()),
        ]);
    }

    public function destroy($category): JsonResponse
    {
        $this->categories->delete($category);

        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, $category): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'integer', 'in:0,1,2'],
        ]);

        $category->update(['status' => $data['status']]);

        return response()->json([
            'message' => __('translate.changeStatusSuccess'),
            'data' => $this->categories->updateStatus($category, (int) $data['status']),
        ]);
    }
}
