<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MediaRepository
{
    public function allAdminUploads(): Collection
    {
        return DB::table('media')
            ->where('collection_name', 'admin_uploads')
            ->latest()
            ->get();
    }

    public function createAdminUpload(array $data): object
    {
        $id = DB::table('media')->insertGetId($data);

        return DB::table('media')->where('id', $id)->first();
    }

    public function findAdminUpload(int|string $id): ?object
    {
        return DB::table('media')
            ->where('collection_name', 'admin_uploads')
            ->where('id', $id)
            ->first();
    }

    public function delete(int|string $id): void
    {
        DB::table('media')->where('id', $id)->delete();
    }
}
