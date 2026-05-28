@extends('admin.layout.app')
@section('title', $title ?? __('translate.orders'))
@section('content')
    <div
        data-react-component="AdminOrderManager"
        data-props='@json($componentProps ?? [])'
    ></div>
@endsection
