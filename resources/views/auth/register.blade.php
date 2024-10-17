@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Register</h2>

                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <label for="avatar" class="form-label">Profile Image</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                    id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </form>

                        <div class="mt-4">
                            <p class="text-center">Or register with:</p>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('social.register', 'google') }}" class="btn btn-outline-danger me-2">
                                    <i class="fab fa-google"></i> Google
                                </a>
                                <a href="{{ route('social.register', 'facebook') }}" class="btn btn-outline-primary">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </a>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-muted">Already have an account? Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
