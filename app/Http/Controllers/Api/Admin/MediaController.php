<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->media->all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->media->store($data['image'], $request->user('admin')?->id),
        ], 201);
    }

    public function destroy($media): JsonResponse
    {
        $this->media->delete($media);

        return response()->json(null, 204);
    }
}
