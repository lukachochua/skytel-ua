@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">User Profile</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email', $user->email) }}" readonly>
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                    name="phone_number" value="{{ old('phone_number', $userInfo->phone_number ?? '') }}" required>
                @error('phone_number')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                    name="address" value="{{ old('address', $userInfo->address ?? '') }}">
                @error('address')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="avatar" class="form-label">Profile Picture</label>
                <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar"
                    name="avatar" accept="image/*">
                @error('avatar')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror

                @if ($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Profile Picture" class="img-thumbnail mt-2"
                        style="max-width: 150px;">
                @else
                    <p>No profile picture uploaded.</p>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
@endsection
