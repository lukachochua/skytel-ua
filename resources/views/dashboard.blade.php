@extends('layouts.app')


@section('content')
    <div class="container">
        <h2>Welcome to your dashboard, {{ Auth::user()->name }}</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
@endsection
