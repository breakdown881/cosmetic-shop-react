<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>@lang('translate.loginPage')</title>
  <link href="{{asset("")}}/adm/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="{{asset("")}}/adm/css/sb-admin.css" rel="stylesheet">
  <link href="{{asset("")}}/adm/css/admin.css" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/admin.jsx'])
</head>
<body class="bg-dark">
  @php
      $success = request()->session()->pull('success');
      $error = request()->session()->pull('error');
      $message = $error ?: $success;
      $type = $error ? 'error' : 'success';
  @endphp
  <div
      data-react-component="AdminLoginPage"
      data-props='@json([
          "action" => route("admin.login"),
          "csrfToken" => csrf_token(),
          "logoUrl" => asset("adm/images/logo.jpg"),
          "alerts" => [
              "message" => $message,
              "type" => $type,
              "errors" => $errors->any() ? $errors->all() : [],
          ],
          "labels" => [
              "email" => __("translate.email"),
              "password" => __("translate.password"),
              "rememberMe" => __("translate.rememberMe"),
              "login" => __("translate.login"),
              "logoAlt" => __("translate.brand"),
          ],
      ])'
  ></div>

  <script src="{{asset("")}}/adm/vendor/jquery/jquery.min.js"></script>
  <script src="{{asset("")}}/adm/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="{{asset("")}}/adm/vendor/jquery-easing/jquery.easing.min.js"></script>
</body>
</html>
