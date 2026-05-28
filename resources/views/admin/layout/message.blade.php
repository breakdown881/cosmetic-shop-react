@php
    $success = request()->session()->pull('success');
    $error = request()->session()->pull('error');
    $message = $error ?: $success;
    $type = $error ? 'error' : 'success';
    $alertMessageProps = [
        'message' => $message,
        'type' => $type,
        'errors' => $errors->any() ? $errors->all() : [],
    ];
@endphp

<div
    data-react-component="AlertMessages"
    data-props='@json($alertMessageProps)'
></div>
