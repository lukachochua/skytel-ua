<!-- File: resources/views/auth/login.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card login-card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Login</h2>

                        <!-- Google Login Button -->
                        <a href="{{ route('google.login') }}" class="btn btn-danger btn-block mb-3">
                            <i class="fab fa-google me-2"></i> Login with Google
                        </a>

                        <div class="text-center mb-3">
                            <span class="text-muted">or</span>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                                    autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}" class="text-muted">
                                    Forgot Your Password?
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .login-card {
            margin-top: 2rem;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: none !important;
            transform: none !important;
        }

        .login-card:hover {
            transform: none !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .card-title {
            color: #333;
            font-weight: 300;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .btn-danger {
            background-color: #db4437;
            border-color: #db4437;
        }

        .btn-danger:hover {
            background-color: #c23321;
            border-color: #c23321;
        }
    </style>
@endpush
