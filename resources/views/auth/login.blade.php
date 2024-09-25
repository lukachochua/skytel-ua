@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card login-card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Login</h2>

                        <div class="d-flex justify-content-between mb-3">
                            <a href="{{ route('google.login') }}"
                                class="btn btn-danger w-100 d-flex align-items-center justify-content-center me-2">
                                <i class="fab fa-google me-2"></i> Login with Google
                            </a>

                            <a href="{{ route('facebook.login') }}"
                                class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="fab fa-facebook me-2"></i> Login with Facebook
                            </a>
                        </div>

                        <div class="text-center mb-3">
                            <span class="text-muted">or</span>
                        </div>

                        <form method="POST" action="{{ route('login.submit') }}">
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

                        <div class="text-center mt-3">
                            <a href="{{ route('register') }}" class="text-muted">Don't have an account? Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
