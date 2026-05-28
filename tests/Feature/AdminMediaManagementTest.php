<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminMediaManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_manager_can_open_media_page_with_real_media_items(): void
    {
        $admin = $this->admin('MANAGER');
        $this->mediaRow($admin, 'admin-media/existing.jpg');

        $this->actingAs($admin, 'admin')
            ->get('/admin/images')
            ->assertOk()
            ->assertSee('existing.jpg')
            ->assertSee('admin\/api\/media', false);

        $this->actingAs($admin, 'admin')
            ->getJson('/admin/api/media')
            ->assertOk()
            ->assertJsonPath('data.0.alt', 'existing.jpg');
    }

    public function test_manager_can_upload_and_delete_media(): void
    {
        Storage::fake('public');
        $admin = $this->admin('MANAGER');

        $this->actingAs($admin, 'admin')
            ->post('/admin/api/media', [
                'image' => UploadedFile::fake()->image('serum.jpg'),
            ])->assertCreated()
            ->assertJsonPath('data.alt', 'serum');

        $media = DB::table('media')->where('collection_name', 'admin_uploads')->first();

        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->file_name);

        $this->actingAs($admin, 'admin')
            ->deleteJson("/admin/api/media/{$media->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->file_name);

        $this->actingAs($admin, 'admin')
            ->post('/admin/images', ['image' => UploadedFile::fake()->image('legacy.jpg')])
            ->assertStatus(405);
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' Media Admin',
            'email' => 'media-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function mediaRow(Admin $admin, string $fileName): void
    {
        DB::table('media')->insert([
            'model_type' => Admin::class,
            'model_id' => $admin->id,
            'uuid' => (string) Str::uuid(),
            'collection_name' => 'admin_uploads',
            'name' => basename($fileName),
            'file_name' => $fileName,
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => 1000,
            'manipulations' => '[]',
            'custom_properties' => '[]',
            'generated_conversions' => '[]',
            'responsive_images' => '[]',
            'order_column' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
