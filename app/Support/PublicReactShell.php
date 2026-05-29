<?php

namespace App\Support;

use App\Services\Customer\CustomerHomeService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Vite;

class PublicReactShell
{
    public function __construct(private readonly CustomerHomeService $homeService) {}

    public function welcome(): Response
    {
        $props = $this->homeService->props();
        $jsonProps = htmlspecialchars(json_encode($props, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8');
        $vite = Vite::useHotFile(public_path('hot'))(['resources/css/public.css', 'resources/js/public.jsx']);

        return response(<<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Goda Shop</title>
    {$vite}
</head>
<body class="customer-site antialiased">
    <div id="react-public-shell" data-react-component="Home" data-props='{$jsonProps}'></div>
</body>
</html>
HTML);
    }
}
