@extends('admin.layout.app')
@section('title', $title ?? __('translate.management'))
@section('content')
    <div
        data-react-component="AdminApiResourceManager"
        data-props='@json($componentProps ?? [])'
    ></div>
@endsection
