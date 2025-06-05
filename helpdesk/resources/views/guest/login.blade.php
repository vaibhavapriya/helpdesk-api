@extends('components.layouts.app.client')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
    <h2 class="mb-4">Login</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login-p') }}">
        @csrf

        <div class="mb-3">
            <label>Email address</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label">Remember Me</label>
        </div>

        <div class="mb-3">
            <a href="{{ route('password.request') }}">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p class="mt-3">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
    </div>
    </div>
</div>
@endsection