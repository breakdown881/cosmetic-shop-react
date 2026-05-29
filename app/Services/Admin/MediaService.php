<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Admin\MediaRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function __construct(private readonly MediaRepository $media) {}

    public function all(): Collection
    {
        return $this->media->allAdminUploads()
            ->map(fn (object $media) => $this->format($media))
            ->values();
    }

    public function store(UploadedFile $file, ?int $adminId): array
    {
        $fileName = $file->store('admin-media', 'public');
        $media = $this->media->createAdminUpload([
            'model_type' => Admin::class,
            'model_id' => $adminId ?? 0,
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

        return $this->format($media);
    }

    public function delete(int|string $id): void
    {
        $media = $this->media->findAdminUpload($id);

        abort_if(! $media, 404);

        Storage::disk($media->disk)->delete($media->file_name);
        $this->media->delete($media->id);
    }

    private function format(object $media): array
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
