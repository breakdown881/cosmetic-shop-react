@php
    $success = request()->session()->pull('success');
    $error = request()->session()->pull('error');
    $message = $error ?: $success;
    $type = $error ? 'error' : 'success';
@endphp

<div
    data-react-component="AlertMessages"
    data-props='@json([
        "message" => $message,
        "type" => $type,
        "errors" => $errors->any() ? $errors->all() : [],
    ])'
></div>
