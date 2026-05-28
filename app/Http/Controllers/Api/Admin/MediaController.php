<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => DB::table('media')
                ->where('collection_name', 'admin_uploads')
                ->latest()
                ->get()
                ->map(fn ($media) => $this->formatMedia($media))
                ->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $file = $data['image'];
        $fileName = $file->store('admin-media', 'public');
        $id = DB::table('media')->insertGetId([
            'model_type' => Admin::class,
            'model_id' => Auth::guard('admin')->id() ?? 0,
            'uuid' => (string) Str::uuid(),
            'collection_name' => 'admin_uploads',
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => $file->getMimeType(),
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => $file->getSize(),
            'manipulations' => '[]',
            'custom_properties' => '[]',
            'generated_conversions' => '[]',
            'responsive_images' => '[]',
            'order_column' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $media = DB::table('media')->where('id', $id)->first();

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->formatMedia($media),
        ], 201);
    }

    public function destroy(int $media): JsonResponse
    {
        $mediaItem = DB::table('media')
            ->where('collection_name', 'admin_uploads')
            ->where('id', $media)
            ->first();

        abort_if(! $mediaItem, 404);

        Storage::disk($mediaItem->disk)->delete($mediaItem->file_name);
        DB::table('media')->where('id', $mediaItem->id)->delete();

        return response()->json(null, 204);
    }

    private function formatMedia($media): array
    {
        return [
            'id' => $media->id,
            'src' => Storage::disk($media->disk)->url($media->file_name),
            'alt' => $media->name,
            'file_name' => $media->file_name,
            'created_at' => (string) $media->created_at,
        ];
    }
}
