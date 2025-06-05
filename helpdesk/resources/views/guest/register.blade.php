@extends('components.layouts.app.client')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Register</h2>
    <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
    <form method="POST" action="{{ route('register-p') }}">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label>Email address</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p class="mt-3">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
    </div>
</div>
@endsection
