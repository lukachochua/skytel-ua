@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-secondary shadow-lg mt-5">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Information</h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $user->name) }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $user->email) }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                value="{{ old('phone_number', $userInfo->phone_number ?? '') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $userInfo->address ?? '') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Profile Picture</label>
                            <div class="mt-2">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="Profile Picture" class="img-thumbnail"
                                        style="max-width: 150px; border-radius: 50%;">
                                @else
                                    <p>No profile picture uploaded.</p>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
