<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $items = DB::table('media')
            ->where('collection_name', 'admin_uploads')
            ->latest()
            ->get()
            ->map(fn ($media) => [
                'id' => $media->id,
                'src' => Storage::disk($media->disk)->url($media->file_name),
                'alt' => $media->name,
            ])
            ->values();

        return \App\Support\AdminReactShell::render('AdminMediaManager', [
                'apiUrl' => route('admin.api.media.index'),
                'items' => $items,
                'labels' => [
                    'delete' => __('translate.delete'),
                    'image' => __('translate.images'),
                    'management' => __('translate.management'),
                    'upload' => 'Upload',
                    'uploadImage' => 'Upload hình',
                    'preview' => 'Xem trước',
                    'empty' => 'Chưa có hình ảnh.',
                ],
        ], 'images', __('translate.images'));
    }
}
