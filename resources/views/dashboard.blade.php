@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Welcome to your dashboard, {{ Auth::user()->name }}</h2>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>
@endsection
