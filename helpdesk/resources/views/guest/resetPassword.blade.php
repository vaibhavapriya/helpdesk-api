@extends('components.layouts.app.client')
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Reset Password</h2>
    <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div class="mb-3">
            <label>Email address</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label>New Password</label>
            <input type="password" class="form-control" name="password" required>
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
    </div>
    </div>
</div>
@endsection
