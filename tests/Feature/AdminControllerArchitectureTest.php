<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminControllerArchitectureTest extends TestCase
{
    public function test_admin_api_controllers_delegate_persistence_to_services(): void
    {
        $violations = [];

        foreach (glob(app_path('Http/Controllers/Api/Admin/*.php')) as $controller) {
            $contents = file_get_contents($controller);
            $name = basename($controller);

            if (preg_match('/^use App\\\\Models\\\\/m', $contents)) {
                $violations[] = "{$name} imports models directly.";
            }

            if (preg_match('/^use Illuminate\\\\Support\\\\Facades\\\\(?:Auth|DB|Mail|Storage);/m', $contents)) {
                $violations[] = "{$name} imports infrastructure facades directly.";
            }
        }

        $this->assertSame([], $violations);
    }
}
