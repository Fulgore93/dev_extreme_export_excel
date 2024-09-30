@extends('layout')
@section('content')
    <div id="container" data-list="{{route('list')}}"></div>
@endsection
@section('js')
    <script src="{{ asset("/js/web/index.js") }}"></script>
@endsection
