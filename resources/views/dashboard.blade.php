@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-header">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="card card-primary shadow-lg mt-3 bg-secondary">
                            {{-- <h2 class="text-center">Welcome to your dashboard, {{ Auth::user()->name }}</h2> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
