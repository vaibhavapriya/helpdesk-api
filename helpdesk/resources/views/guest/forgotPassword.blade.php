@extends('components.layouts.app.client')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
    <h2 class="mb-4">Forgot Password</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.request') }}">
        @csrf

        <div class="mb-3">
            <label>Email address</label>
            <input type="email" class="form-control" name="email" required autofocus>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
    </form>
    </div>
    </div>
</div>
@endsection
