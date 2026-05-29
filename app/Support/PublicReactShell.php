<?php

namespace App\Support;

use App\Services\Customer\CustomerHomeService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;

class PublicReactShell
{
    public function __construct(private readonly CustomerHomeService $homeService) {}

    public function welcome(): Response
    {
        return $this->render('Home', $this->homeService->props(), 'Goda Shop');
    }

    public function render(string $component, array $props, string $title = 'Goda Shop'): Response
    {
        $props['auth'] ??= $this->authProps();
        $jsonProps = htmlspecialchars(json_encode($props, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8');
        $reactRefresh = Vite::reactRefresh();
        $vite = Vite::useHotFile(public_path('hot'))(['resources/css/public.css', 'resources/js/public.jsx']);
        $escapedComponent = htmlspecialchars($component, ENT_QUOTES, 'UTF-8');
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        return response(<<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$escapedTitle}</title>
    {$reactRefresh}
    {$vite}
</head>
<body class="customer-site antialiased">
    <div id="react-public-shell" data-react-component="{$escapedComponent}" data-props='{$jsonProps}'></div>
</body>
</html>
HTML);
    }

    private function authProps(): array
    {
        $user = Auth::user();

        return [
            'check' => $user !== null,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ] : null,
            'loginUrl' => '/login',
            'registerUrl' => '/register',
            'logoutUrl' => '/logout',
        ];
    }
}
