@extends('layouts.app')

@section('content')

    <div id="app">
        <chat-page-component></chat-page-component>
    </div>

@endsection

@push('css')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="{{asset('js/jquery.nicescroll.min.js')}}" defer></script>

    <script>
        $(document).ready(function () {
            $(".chat").niceScroll();

        })
    </script>
@endpush
