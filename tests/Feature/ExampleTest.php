<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('id="react-public-shell"', false)
            ->assertSee('data-react-component="PublicWelcomePage"', false);
    }

    public function test_web_routes_do_not_render_blade_views_for_frontend_shells(): void
    {
        $this->assertStringNotContainsString("return view('welcome')", file_get_contents(base_path('routes/web.php')));
    }
}
