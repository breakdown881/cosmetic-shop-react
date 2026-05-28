<!DOCTYPE html>
<html>
    <head>
        <title>Trang chủ - Mỹ Phẩm Goda</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/x-icon" href="{{asset("")}}/images/logo.jpg" />
        <link rel="stylesheet" href="{{asset("")}}/vendor/fontawesome-free-5.11.2-web/css/all.min.css">
        <link rel="stylesheet" href="{{asset("")}}/vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{asset("")}}/vendor/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css">
        <link rel="stylesheet" href="{{asset("")}}/vendor/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css">
        <link rel="stylesheet" href="{{asset("")}}/vendor/star-rating/css/star-rating.min.css">
        <link rel="stylesheet" href="{{asset("")}}/css/style.css">
        @vite(['resources/css/app.css', 'resources/js/public.jsx'])
        <script src="{{asset("")}}/vendor/jquery.min.js"></script>
        <script src="{{asset("")}}/vendor/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{asset("")}}/vendor/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>
        <script type="text/javascript" src="{{asset("")}}/vendor/star-rating/js/star-rating.min.js"></script>
        {!! NoCaptcha::renderJs() !!}
        <script src="{{asset("")}}/vendor/format/number_format.js"></script>
        <script src="{{asset("")}}/vendor/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="{{asset("")}}/js/script.js"></script>
    </head>
    <body>
        @php
            $currentRouteName = Route::currentRouteName();
        @endphp
        @php
            $currentRouteName = Route::currentRouteName();

            if (Auth::check()) {
                Cart::destroy();
                Cart::restore(Auth()->user()->email);
                Cart::store(Auth()->user()->email);
            }

            $publicSocialLinks = [
                ['href' => 'https://www.facebook.com/HocLapTrinhWebTaiNha.ThayLoc', 'icon' => 'fab fa-facebook-f'],
                ['href' => 'https://twitter.com', 'icon' => 'fab fa-twitter'],
                ['href' => 'https://www.instagram.com', 'icon' => 'fab fa-instagram'],
                ['href' => 'https://www.pinterest.com/', 'icon' => 'fab fa-pinterest'],
                ['href' => 'https://www.youtube.com/', 'icon' => 'fab fa-youtube'],
            ];
        @endphp
        <div
            data-react-component="PublicHeader"
            data-props='@json([
                "activeRoute" => $currentRouteName,
                "cartCount" => Cart::count(),
                "csrfToken" => csrf_token(),
                "isAuthenticated" => Auth::check(),
                "userName" => Auth::check() ? Auth::user()->name : "",
                "searchValue" => request()->has("search") ? request()->input("search") : "",
                "socialLinks" => $publicSocialLinks,
                "urls" => [
                    "home" => route("index"),
                    "products" => route("product.index"),
                    "contact" => route("contact.show"),
                    "customerOrders" => Auth::check() ? route("customer.orders") : "#",
                    "customerShow" => Auth::check() ? route("customer.show") : "#",
                    "customerAddress" => Auth::check() ? route("customer.address") : "#",
                    "logout" => route("logout"),
                    "logoImage" => asset("images/goda450x170_1.jpg"),
                    "bannerImage" => asset("images/godakeben450x170.jpg"),
                ],
                "labels" => [
                    "home" => "Trang ch?",
                    "products" => "S?n ph?m",
                    "returnPolicy" => "Ch?nh s?ch ??i tr?",
                    "paymentPolicy" => "Ch?nh s?ch thanh to?n",
                    "shippingPolicy" => "Ch?nh s?ch giao h?ng",
                    "contact" => "Li?n h?",
                    "myOrders" => "??n h?ng c?a t?i",
                    "register" => "??ng K?",
                    "login" => "??ng Nh?p",
                    "accountInfo" => "Th?ng tin t?i kho?n",
                    "shippingAddress" => "??a ch? giao h?ng",
                    "logout" => "Tho?t",
                    "searchPlaceholder" => "Nh?p t? kh?a t?m ki?m",
                    "experience" => "Tr?i nghi?m c?ng s?n ph?m c?a Goda",
                    "cart" => "Gi? H?ng",
                ],
            ])'
        ></div>
        @include('layout.message');
        @yield('content')
        <div
            data-react-component="PublicFooter"
            data-props='@json([
                "categoryLinks" => [
                    ["href" => "#", "label" => "Kem Ch?ng N?ng"],
                    ["href" => "#", "label" => "Kem D??ng Da"],
                    ["href" => "#", "label" => "Kem Tr? M?n"],
                    ["href" => "#", "label" => "Kem Tr? Th?m N?m"],
                    ["href" => "#", "label" => "S?a R?a M?t"],
                    ["href" => "#", "label" => "S?a T?m"],
                ],
                "policyLinks" => [
                    ["href" => "san-pham.html", "label" => "S?n ph?m"],
                    ["href" => "chinh-sach-doi-tra.html", "label" => "Ch?nh s?ch ??i tr?"],
                    ["href" => "chinh-sach-thanh-toan.html", "label" => "Ch?nh s?ch thanh to?n"],
                    ["href" => "chinh-sach-giao-hang.html", "label" => "Ch?nh s?ch giao h?ng"],
                    ["href" => "lien-he.html", "label" => "Li?n h?"],
                ],
                "socialLinks" => $publicSocialLinks,
                "labels" => [
                    "categories" => "Danh m?c",
                    "links" => "Li?n k?t",
                    "contactUs" => "Li?n h? v?i ch?ng t?i",
                    "newsletter" => "B?n tin",
                    "newsletterDescription" => "Nh?p Email c?a b?n ?? ch?ng t?i cung c?p th?ng tin nhanh nh?t cho b?n v? nh?ng s?n ph?m m?i!!",
                    "emailPlaceholder" => "Nh?p email c?a b?n..",
                    "send" => "G?i",
                ],
            ])'
        ></div>
        <div
            data-react-component="PublicAuthModals"
            data-props='@json([
                "registerUrl" => route("register"),
                "loginUrl" => route("login"),
                "forgotPasswordUrl" => route("password.email"),
                "googleLoginUrl" => route("google.login"),
                "facebookLoginUrl" => route("facebook.login"),
                "csrfToken" => csrf_token(),
                "captchaHtml" => (string) app("captcha")->display(),
                "labels" => [
                    "registerTitle" => "??ng k?",
                    "loginTitle" => "??ng nh?p",
                    "forgotPasswordTitle" => "Qu?n m?t kh?u",
                    "fullname" => "H? v? t?n",
                    "mobile" => "S? ?i?n tho?i",
                    "password" => "M?t kh?u",
                    "passwordConfirmation" => "Nh?p l?i m?t kh?u",
                    "cancel" => "H?y",
                    "registerButton" => "??ng k?",
                    "loginButton" => "??ng Nh?p",
                    "googleLogin" => "??ng nh?p b?ng Google",
                    "facebookLogin" => "??ng nh?p b?ng Facebook",
                    "registerPrompt" => "B?n ch?a l? th?nh vi?n? ??ng k? ngay!",
                    "forgotPassword" => "Qu?n M?t Kh?u?",
                    "send" => "G?I",
                ],
            ])'
        ></div>
        @php
            $cartModalItems = Cart::content()->map(function ($item, $rowId) {
                return [
                    'rowId' => $rowId,
                    'name' => $item->name,
                    'price' => $item->price,
                    'qty' => $item->qty,
                    'image' => "../images/{$item->options->image}",
                    'url' => '#',
                ];
            })->values();
        @endphp
        <div
            data-react-component="PublicCartModal"
            data-props='@json([
                "items" => $cartModalItems,
                "subtotal" => Cart::subtotal() . "?",
                "cartUrl" => route("product.index"),
                "checkoutUrl" => route("payment.create"),
                "labels" => [
                    "title" => "Gi? h?ng",
                    "product" => "S?n ph?m",
                    "price" => "??n gi?",
                    "quantity" => "S? l??ng",
                    "subtotal" => "Th?nh ti?n",
                    "total" => "T?ng ti?n",
                    "continueShopping" => "Ti?p t?c mua s?m",
                    "checkout" => "??t h?ng",
                ],
            ])'
        ></div>
        <!-- END CART DIALOG -->
        <!-- Facebook Messenger Chat -->
        <!-- Load Facebook SDK for JavaScript -->
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
              FB.init({
                xfbml            : true,
                version          : 'v4.0'
            });
            };

            (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <!-- Your customer chat code -->
        <div class="fb-customerchat"
            attribution=setup_tool
            page_id="112296576811987"
            logged_in_greeting="Chào bạn, bạn muốn mua sản phẩm nào bên GodaShop.com"
            logged_out_greeting="Chào bạn, bạn muốn mua sản phẩm nào bên GodaShop.com">
        </div>
        <!-- End Facebook Messenger Chat -->
        <script src="https://apis.google.com/js/platform.js" async defer></script>
    </body>
</html>
