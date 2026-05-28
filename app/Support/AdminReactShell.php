<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;

class AdminReactShell
{
    public static function render(string $component, array $props = [], string $currentMenu = '', string $title = ''): \Illuminate\Http\Response
    {
        return response(self::html([
            'component' => 'AdminAppShell',
            'id' => 'react-admin-shell',
            'props' => [
                'footer' => self::footerProps(),
                'page' => [
                    'component' => $component,
                    'props' => $props,
                ],
                'sidebar' => ['items' => self::sidebarItems($currentMenu)],
                'topNav' => self::topNavProps(),
            ],
            'title' => $title ?: __('translate.overview'),
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
        $bodyClass = self::escape($options['bodyClass'] ?? '');
        $component = self::escape($options['component']);
        $id = self::escape($options['id']);
        $props = htmlspecialchars(
            json_encode($options['props'], JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG),
            ENT_NOQUOTES,
            'UTF-8'
        );
        $csrf = self::escape(csrf_token());
        $title = self::escape($options['title']);
        $vite = Vite::useHotFile(public_path('hot'))(['resources/css/app.css', 'resources/js/admin.jsx']);

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

    private static function topNavProps(): array
    {
        return [
            'brandUrl' => route('admin.dashboard'),
            'userName' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : '',
            'labels' => [
                'brand' => __('translate.brand'),
                'hello' => 'Chào',
                'logout' => __('translate.logout'),
            ],
        ];
    }

    private static function footerProps(): array
    {
        return [
            'logoutUrl' => route('admin.logout'),
            'csrfToken' => csrf_token(),
            'labels' => [
                'copyright' => 'Copyright Hoàng Hải',
                'exitConfirm' => __('translate.exitConfirm'),
                'cancel' => __('translate.cancel'),
                'exit' => __('translate.exit'),
            ],
        ];
    }

    private static function sidebarItems(string $currentMenu): array
    {
        $canWriteDiscounts = in_array(Auth::guard('admin')->user()?->role, ['MANAGER', 'ADMIN'], true);
        $canWriteFeeShips = in_array(Auth::guard('admin')->user()?->role, ['MANAGER', 'ADMIN'], true);
        $discountChildren = [
            ['label' => __('translate.list'), 'href' => route('admin.discount.index'), 'active' => request()->routeIs('admin.discount.index')],
        ];
        $feeShipChildren = [
            ['label' => __('translate.list'), 'href' => route('admin.feeship.index'), 'active' => request()->routeIs('admin.feeship.index')],
        ];

        if ($canWriteDiscounts) {
            $discountChildren[] = ['label' => __('translate.add'), 'href' => route('admin.discount.create'), 'active' => request()->routeIs('admin.discount.create')];
        }

        if ($canWriteFeeShips) {
            $feeShipChildren[] = ['label' => __('translate.add'), 'href' => route('admin.feeship.create'), 'active' => request()->routeIs('admin.feeship.create')];
        }

        return [
            ['label' => __('translate.overview'), 'href' => route('admin.dashboard'), 'icon' => 'fas fa-fw fa-tachometer-alt', 'active' => request()->routeIs('admin.dashboard')],
            ['label' => __('translate.orders'), 'icon' => 'fas fa-shopping-cart', 'open' => $currentMenu === 'orders', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.order.index'), 'active' => request()->routeIs('admin.order.index')],
                ['label' => __('translate.add'), 'href' => route('admin.order.create'), 'active' => request()->routeIs('admin.order.create')],
            ]],
            ['label' => __('translate.products'), 'icon' => 'fab fa-product-hunt', 'open' => $currentMenu === 'products', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.product.index'), 'active' => request()->routeIs('admin.product.index')],
                ['label' => __('translate.add'), 'href' => route('admin.product.create'), 'active' => request()->routeIs('admin.product.create')],
            ]],
            ['label' => __('translate.comments'), 'icon' => 'fas fa-comments', 'open' => $currentMenu === 'comments', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.comments.index'), 'active' => request()->routeIs('admin.comments.index')],
            ]],
            ['label' => __('translate.images'), 'icon' => 'far fa-image', 'open' => $currentMenu === 'images', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.media.index'), 'active' => request()->routeIs('admin.media.index')],
            ]],
            ['label' => __('translate.customers'), 'icon' => 'fas fa-user-alt', 'open' => $currentMenu === 'customers', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.customer.index'), 'active' => request()->routeIs('admin.customer.index')],
            ]],
            ['label' => __('translate.brands'), 'icon' => 'fas fa-folder', 'open' => $currentMenu === 'brands', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.brand.index'), 'active' => request()->routeIs('admin.brand.index')],
                ['label' => __('translate.add'), 'href' => route('admin.brand.create'), 'active' => request()->routeIs('admin.brand.create')],
            ]],
            ['label' => __('translate.categories'), 'icon' => 'fas fa-folder', 'open' => $currentMenu === 'categories', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.category.index'), 'active' => request()->routeIs('admin.category.index')],
                ['label' => __('translate.add'), 'href' => route('admin.category.create'), 'active' => request()->routeIs('admin.category.create')],
            ]],
            ['label' => __('translate.discounts'), 'icon' => 'fas fa-percentage', 'open' => $currentMenu === 'discounts', 'children' => $discountChildren],
            ['label' => __('translate.feeShips'), 'icon' => 'fas fa-shipping-fast', 'open' => $currentMenu === 'feeships', 'children' => $feeShipChildren],
            ['label' => __('translate.staffs'), 'icon' => 'fas fa-users', 'open' => $currentMenu === 'staffs', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.staff.index'), 'active' => request()->routeIs('admin.staff.index')],
                ['label' => __('translate.add'), 'href' => route('admin.staff.create'), 'active' => request()->routeIs('admin.staff.create')],
            ]],
            ['label' => __('translate.authorizations'), 'icon' => 'fas fa-user-shield', 'open' => $currentMenu === 'roles', 'children' => [
                ['label' => __('translate.listRole'), 'href' => route('admin.role.index'), 'active' => request()->routeIs('admin.role.index')],
                ['label' => __('translate.add'), 'href' => route('admin.role.create'), 'active' => request()->routeIs('admin.role.create')],
            ]],
            ['label' => __('translate.newsLetter'), 'icon' => 'fas fa-file-alt', 'open' => $currentMenu === 'newsletters', 'children' => [
                ['label' => __('translate.list'), 'href' => route('admin.newsletter.index'), 'active' => request()->routeIs('admin.newsletter.index')],
                ['label' => __('translate.sendMail'), 'href' => route('admin.newsletter.index')],
            ]],
        ];
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
