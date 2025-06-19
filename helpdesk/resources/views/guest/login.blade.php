@extends('components.layouts.app.client')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
    <h2 class="mb-4">Login</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form id="l">

        <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <a href="{{ route('fp') }}">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p class="mt-3">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
    </div>
    </div>
</div>
@endsection

<script>
    const token = 'Bearer ' + localStorage.getItem('auth_token')?? null;
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("l").addEventListener("submit", async function(e) {
        e.preventDefault();
        //const formData = new FormData(this);

        const formData = {
            email: e.target.email.value,
            password: e.target.password.value,
            remember: e.target.remember.checked
        };

        // const response = await fetch('http://127.0.0.1:8000/api/login', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         'Accept': 'application/json',
        //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        //     },
        //     body: JSON.stringify(data)
        // });

        try {
            const response = await fetch('http://127.0.0.1:8000/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': token
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const error = await response.json();
                console.error(error);
                alert(error.message || "Login failed");
                return;
            }

            const data = await response.json();
            // console.log('Login:', data);

            // Store token if using token-based API
            if (data.success===true) {
                localStorage.setItem('auth_token', data.meta.token);
                localStorage.setItem('user_id', data.data.id);
                localStorage.setItem('user_role', data.data.role);
                //Redirect
                window.location.href = data.redirect || '/';
            }


        } catch (err) {
            console.error("Unexpected error:", err);
            alert("An unexpected error occurred.");
        }
    });
});
</script>
