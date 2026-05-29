<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiCatalogControllerArchitectureTest extends TestCase
{
    public function test_catalog_api_controllers_delegate_persistence_to_services(): void
    {
        $controllers = [
            app_path('Http/Controllers/Api/BrandController.php'),
            app_path('Http/Controllers/Api/CategoryController.php'),
            app_path('Http/Controllers/Api/ProductController.php'),
        ];
        $violations = [];

        foreach ($controllers as $controller) {
            $contents = file_get_contents($controller);
            $name = basename($controller);

            if (preg_match('/^use App\\\\Models\\\\/m', $contents)) {
                $violations[] = "{$name} imports models directly.";
            }
        }

        $this->assertSame([], $violations);
    }
}
