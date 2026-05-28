<?php

namespace Tests\Feature;

use App\Mail\NewsletterMessage;
use App\Models\Admin;
use App\Models\NewsLetter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminNewsletterManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_manager_can_view_subscribers_and_send_newsletter(): void
    {
        Mail::fake();
        $admin = $this->admin('MANAGER');
        NewsLetter::create([
            'email' => 'subscriber@example.test',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/newsletters')
            ->assertOk()
            ->assertSee('AdminNewsletterManager')
            ->assertSee('admin\/api\/newsletters', false);

        $this->actingAs($admin, 'admin')
            ->getJson('/admin/api/newsletters')
            ->assertOk()
            ->assertJsonPath('data.0.email', 'subscriber@example.test');

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/api/newsletters/send', [
                'subject' => 'Sale thang nay',
                'body' => 'Noi dung khuyen mai',
            ])->assertOk()
            ->assertJsonPath('message', 'Newsletter sent.');

        Mail::assertSent(NewsletterMessage::class, function (NewsletterMessage $mail) {
            return $mail->hasTo('subscriber@example.test')
                && $mail->subjectText === 'Sale thang nay'
                && $mail->bodyText === 'Noi dung khuyen mai';
        });

        $this->actingAs($admin, 'admin')
            ->post('/admin/newsletters/send', [
                'subject' => 'Legacy',
                'body' => 'Legacy body',
            ])->assertNotFound();
    }

    public function test_staff_cannot_manage_newsletters(): void
    {
        $this->actingAs($this->admin('STAFF'), 'admin')
            ->get('/admin/newsletters')
            ->assertForbidden();
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' Newsletter Admin',
            'email' => 'newsletter-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
