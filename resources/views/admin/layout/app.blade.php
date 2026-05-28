<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('translate.overview'))</title>
    <!-- Create favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('') }}/adm/images/logo.jpg">
    <!-- Custom fonts for this template-->
    <link href="{{ asset('') }}/adm/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="{{ asset('') }}/adm/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom styles for this template-->
    <link href="{{ asset('') }}/adm/css/sb-admin.css" rel="stylesheet">
    <link href="{{ asset('') }}/adm/css/admin.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/admin.jsx'])
</head>

<body id="page-top">
    @php
        $adminTopNavProps = [
            'brandUrl' => route('admin.dashboard'),
            'userName' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : 'Hoàng Hải',
            'labels' => [
                'brand' => __('translate.brand'),
                'hello' => 'Chào',
                'logout' => __('translate.logout'),
            ],
        ];
    @endphp
    <div
        data-react-component="AdminTopNav"
        data-props='@json($adminTopNavProps)'
    ></div>
    <div id="wrapper">
        @php
            $segments = request()->segments();
            $isBrand = ($currentMenu ?? '') === 'brands';
            $isCategory = ($currentMenu ?? '') === 'categories';
            $isProduct = ($currentMenu ?? '') === 'products';
            $isCustomer = ($currentMenu ?? '') === 'customers';
            $isStaff = ($currentMenu ?? '') === 'staffs';
            $isRole = ($currentMenu ?? '') === 'roles';
            $adminSidebarItems = [
                [
                    'label' => __('translate.overview'),
                    'href' => route('admin.dashboard'),
                    'icon' => 'fas fa-fw fa-tachometer-alt',
                    'active' => request()->routeIs('admin.dashboard'),
                ],
                [
                    'label' => __('translate.orders'),
                    'icon' => 'fas fa-shopping-cart',
                    'children' => [
                        ['label' => __('translate.list'), 'href' => '#'],
                        ['label' => __('translate.add'), 'href' => '#'],
                    ],
                ],
                [
                    'label' => __('translate.products'),
                    'icon' => 'fab fa-product-hunt',
                    'open' => $isProduct,
                    'children' => [
                        ['label' => __('translate.list'), 'href' => route('admin.product.index'), 'active' => request()->routeIs('admin.product.index')],
                        ['label' => __('translate.add'), 'href' => route('admin.product.create'), 'active' => request()->routeIs('admin.product.create')],
                    ],
                ],
                [
                    'label' => __('translate.comments'),
                    'icon' => 'fas fa-comments',
                    'children' => [
                        ['label' => __('translate.list'), 'href' => '#'],
                    ],
                ],
                [
                    'label' => __('translate.images'),
                    'icon' => 'far fa-image',
                    'children' => [
                        ['label' => __('translate.list'), 'href' => '#'],
                    ],
                ],
                [
                    'label' => __('translate.customers'),
                    'icon' => 'fas fa-user-alt',
                    'open' => $isCustomer,
                    'children' => [
                        ['label' => __('translate.list'), 'href' => route('admin.customer.index'), 'active' => request()->routeIs('admin.customer.index')],
                    ],
                ],
                [
                    'label' => __('translate.brands'),
                    'icon' => 'fas fa-folder',
                    'open' => $isBrand,
                    'children' => [
                        ['label' => __('translate.list'), 'href' => route('admin.brand.index'), 'active' => request()->is('admin/brands')],
                        ['label' => __('translate.add'), 'href' => route('admin.brand.create'), 'active' => request()->is('admin/brands/*')],
                    ],
                ],
                [
                    'label' => __('translate.categories'),
                    'icon' => 'fas fa-folder',
                    'open' => $isCategory,
                    'children' => [
                        ['label' => __('translate.list'), 'href' => route('admin.category.index'), 'active' => request()->is('admin/categories') || (isset($segments[2]) && is_numeric($segments[2] ?? ''))],
                        ['label' => __('translate.add'), 'href' => route('admin.category.create'), 'active' => isset($segments[2]) && !is_numeric($segments[2])],
                    ],
                ],
                [
                    'label' => __('translate.discounts'),
                    'icon' => 'fas fa-percentage',
                    'children' => [
                        ['label' => __('translate.list'), 'href' => '#'],
                        ['label' => __('translate.add'), 'href' => '#'],
                    ],
                ],
                [
                    'label' => __('translate.feeShips'),
                    'icon' => 'fas fa-shipping-fast',
                    'children' => [
                        ['label' => __('translate.list'), 'href' => '#'],
                        ['label' => __('translate.add'), 'href' => '#'],
                    ],
                ],
                [
                    'label' => __('translate.staffs'),
                    'icon' => 'fas fa-users',
                    'open' => $isStaff,
                    'children' => [
                        ['label' => __('translate.list'), 'href' => route('admin.staff.index'), 'active' => request()->routeIs('admin.staff.index')],
                        ['label' => __('translate.add'), 'href' => route('admin.staff.create'), 'active' => request()->routeIs('admin.staff.create')],
                    ],
                ],
                [
                    'label' => __('translate.authorizations'),
                    'icon' => 'fas fa-user-shield',
                    'open' => $isRole,
                    'children' => [
                        ['label' => __('translate.listRole'), 'href' => route('admin.role.index'), 'active' => request()->routeIs('admin.role.index')],
                        ['label' => __('translate.add'), 'href' => route('admin.role.create'), 'active' => request()->routeIs('admin.role.create')],
                    ],
                ],
                [
                    'label' => __('translate.orderStatus'),
                    'href' => '#',
                    'icon' => 'fas fa-star-half-alt',
                ],
                [
                    'label' => __('translate.newsLetter'),
                    'icon' => 'fas fa-file-alt',
                    'children' => [
                        ['label' => __('translate.list'), 'href' => '#'],
                        ['label' => __('translate.sendMail'), 'href' => '#'],
                    ],
                ],
            ];

            $adminSidebarProps = ['items' => $adminSidebarItems];
        @endphp
        <div
            data-react-component="AdminSidebar"
            data-props='@json($adminSidebarProps)'
        ></div>

        <div id="content-wrapper">
            @include('admin.layout.message')
            @yield('content')
        </div>

        @php
            $adminFooterLogoutProps = [
                'logoutUrl' => route('admin.logout'),
                'csrfToken' => csrf_token(),
                'labels' => [
                    'copyright' => 'Copyright Hoàng Hải',
                    'exitConfirm' => __('translate.exitConfirm'),
                    'cancel' => __('translate.cancel'),
                    'exit' => __('translate.exit'),
                ],
            ];
        @endphp
        <div
            data-react-component="AdminFooterLogout"
            data-props='@json($adminFooterLogoutProps)'
        ></div>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('') }}/adm/vendor/jquery/jquery.min.js"></script>
    <script src="{{ asset('') }}/adm/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('') }}/adm/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <script src="{{ asset('') }}/adm/vendor/datatables/jquery.dataTables.js"></script>
    <script src="{{ asset('') }}/adm/vendor/datatables/dataTables.bootstrap4.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('') }}/adm/js/sb-admin.min.js"></script>
    <!-- Demo scripts for this page-->
    <script src="{{ asset('') }}/adm/js/demo/datatables-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('') }}/adm/js/admin.js"></script>
    @stack('scripts')
</body>

</html>
