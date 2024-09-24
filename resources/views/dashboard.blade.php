@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Welcome to your dashboard, {{ Auth::user()->name }}</h2>

    </div>
@endsection
