<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->brands($request)->map(fn (Brand $brand) => $this->formatBrand($brand)),
        ]);
    }

    public function store(CreateBrandRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user('admin')?->id ?? $request->user()?->id ?? 1;

        $brand = Brand::create($data);

        if ($request->hasFile('image')) {
            $brand->addMediaFromRequest('image')->toMediaCollection('brands');
            $brand->refresh();
        }

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->formatBrand($brand),
        ], 201);
    }

    public function show(Brand $brand): JsonResponse
    {
        return response()->json([
            'data' => $this->formatBrand($brand),
        ]);
    }

    public function update(CreateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand->update($request->validated());

        if ($request->hasFile('image')) {
            $brand->clearMediaCollection('brands');
            $brand->addMediaFromRequest('image')->toMediaCollection('brands');
            $brand->refresh();
        }

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->formatBrand($brand),
        ]);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $brand->clearMediaCollection('brands');
        $brand->delete();

        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, Brand $brand): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'integer', 'in:0,1,2'],
        ]);

        $brand->update(['status' => $data['status']]);

        return response()->json([
            'message' => __('translate.changeStatusSuccess'),
            'data' => $this->formatBrand($brand),
        ]);
    }

    private function formatBrand(Brand $brand): array
    {
        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'status' => (int) $brand->status,
            'created_by' => $brand->created_by,
            'image_url' => $brand->getFirstMediaUrl('brands') ?: null,
            'created_at' => optional($brand->created_at)->toDateTimeString(),
            'updated_at' => optional($brand->updated_at)->toDateTimeString(),
        ];
    }

    private function brands(Request $request)
    {
        if (! $request->filled('q')) {
            $query = Brand::query()->latest();

            if ($request->filled('status')) {
                $query->where('status', $request->integer('status'));
            }

            return $query->get();
        }

        $search = Brand::search((string) $request->input('q'));

        if ($request->filled('status')) {
            $search->where('status', $request->integer('status'));
        }

        return $search->query(function ($query) use ($request) {
            $query->latest()
                ->where('name', 'like', '%' . $request->input('q') . '%');

            if ($request->filled('status')) {
                $query->where('status', $request->integer('status'));
            }
        })->get();
    }
}
