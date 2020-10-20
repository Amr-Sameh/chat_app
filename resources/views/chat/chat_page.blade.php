@extends('layouts.app')

@section('content')

    <div id="app">
        <chat-page-component :current-user="{{ json_encode( auth()->user() ?? null) }}"></chat-page-component>
    </div>

@endsection

@push('css')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
@endpush

