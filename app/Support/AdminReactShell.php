<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;

class AdminReactShell
{
    public static function spa(): \Illuminate\Http\Response
    {
        $admin = Auth::guard('admin')->user();

        return response(self::html([
            'component' => 'AdminSpaApp',
            'id' => 'react-admin-shell',
            'props' => [
                'csrfToken' => csrf_token(),
                'logoutUrl' => route('admin.logout'),
                'role' => $admin?->role,
                'userName' => $admin?->name ?? '',
            ],
            'title' => __('translate.overview'),
        ]));
    }

    public static function login(array $props): \Illuminate\Http\Response
    {
        return response(self::html([
            'bodyClass' => 'bg-dark',
            'component' => 'AdminLoginPage',
            'id' => 'react-admin-login',
            'props' => $props,
            'title' => __('translate.loginPage'),
        ]));
    }

    private static function html(array $options): string
    {
        $bodyClass = self::escape(trim('admin-site ' . ($options['bodyClass'] ?? '')));
        $component = self::escape($options['component']);
        $id = self::escape($options['id']);
        $props = htmlspecialchars(
            json_encode($options['props'], JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG),
            ENT_NOQUOTES,
            'UTF-8'
        );
        $csrf = self::escape(csrf_token());
        $title = self::escape($options['title']);
        $reactRefresh = Vite::reactRefresh();
        $vite = Vite::useHotFile(public_path('hot'))(['resources/css/admin.css', 'resources/js/admin.jsx']);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{$csrf}">
    <title>{$title}</title>
    <link rel="shortcut icon" type="image/x-icon" href="/adm/images/logo.jpg">
    <link href="/adm/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="/adm/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="/adm/css/sb-admin.css" rel="stylesheet">
    <link href="/adm/css/admin.css" rel="stylesheet">
    {$reactRefresh}
    {$vite}
</head>
<body id="page-top" class="{$bodyClass}">
    <div id="{$id}" data-react-component="{$component}" data-props='{$props}'></div>
    <script src="/adm/vendor/jquery/jquery.min.js"></script>
    <script src="/adm/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/adm/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/adm/js/sb-admin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/adm/js/admin.js"></script>
</body>
</html>
HTML;
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
