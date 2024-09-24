@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Provide Additional Information</h2>

    <form action="{{ route('user.info.submit') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
