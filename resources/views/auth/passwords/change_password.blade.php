@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Change Password</h2>

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (Auth::check() && Auth::user()->auth_type === 'email')
                            <form method="POST" action="{{ route('password.change.update') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                    @error('current_password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                    @error('new_password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                    @error('new_password_confirmation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        @else
                            <p>You cannot change your password because you logged in using
                                {{ ucfirst(Auth::user()->auth_type) }}.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
