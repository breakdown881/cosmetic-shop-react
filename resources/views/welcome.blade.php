<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Goda Shop</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/public.jsx'])
    </head>
    <body class="antialiased">
        @php
            $authLinks = [];
            if (Route::has('login')) {
                if (Auth::check()) {
                    $authLinks[] = ['href' => url('/home'), 'label' => 'Home'];
                } else {
                    $authLinks[] = ['href' => route('login'), 'label' => 'Login'];
                    if (Route::has('register')) {
                        $authLinks[] = ['href' => route('register'), 'label' => 'Register'];
                    }
                }
            }

            $publicWelcomeProps = [
                'title' => 'Goda Shop React',
                'description' => 'Frontend đã được chuyển sang React islands qua Laravel Vite.',
                'authLinks' => $authLinks,
                'cards' => [
                    ['title' => 'Sản phẩm', 'description' => 'Khám phá danh sách sản phẩm mỹ phẩm.', 'href' => Route::has('product.index') ? route('product.index') : '#'],
                    ['title' => 'Admin', 'description' => 'Quản trị thương hiệu, danh mục và sản phẩm.', 'href' => Route::has('admin.dashboard') ? route('admin.dashboard') : '#'],
                    ['title' => 'React + Laravel', 'description' => 'Blade giữ vai trò shell/data payload, UI render bằng React.', 'href' => '#'],
                ],
                'version' => 'Laravel v' . Illuminate\Foundation\Application::VERSION . ' (PHP v' . PHP_VERSION . ')',
            ];
        @endphp
        <div
            data-react-component="PublicWelcomePage"
            data-props='@json($publicWelcomeProps)'
        ></div>
    </body>
</html>
