<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;

class PublicReactShell
{
    public static function welcome(): \Illuminate\Http\Response
    {
        $props = [
            'title' => 'Goda Shop React',
            'description' => 'Frontend đã được chuyển sang React qua Laravel Vite.',
            'authLinks' => [],
            'cards' => [
                ['title' => 'Sản phẩm', 'description' => 'Khám phá danh sách sản phẩm mỹ phẩm.', 'href' => Route::has('product.index') ? route('product.index') : '#'],
                ['title' => 'Admin', 'description' => 'Quản trị thương hiệu, danh mục và sản phẩm.', 'href' => '/admin'],
                ['title' => 'React + Laravel', 'description' => 'Laravel cung cấp API, UI render bằng React.', 'href' => '#'],
            ],
            'version' => 'Laravel v' . app()->version() . ' (PHP v' . PHP_VERSION . ')',
        ];

        $jsonProps = htmlspecialchars(json_encode($props, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8');
        $vite = Vite::useHotFile(public_path('hot'))(['resources/css/app.css', 'resources/js/public.jsx']);

        return response(<<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Goda Shop</title>
    {$vite}
</head>
<body class="antialiased">
    <div id="react-public-shell" data-react-component="PublicWelcomePage" data-props='{$jsonProps}'></div>
</body>
</html>
HTML);
    }
}
