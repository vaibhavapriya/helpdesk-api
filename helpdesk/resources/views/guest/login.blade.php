@extends('components.layouts.app.client')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
    <h2 class="mb-4" id="login-title">Login</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form id="login">

        <div class="mb-3">
            <label id="label-email" for="email">Email address</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label id="label-password" for="password">Password</label>
            <input id="password" type="password" class="form-control" name="password" required>
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3 form-check">
            <input id="remember" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" id="label-remember" for="remember">Remember Me</label>
        </div>

        <div class="mb-3">
            <a href="{{ route('fp') }}" id="forgot-password-link">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary" id="login-button">Login</button>
    </form>

    <p class="mt-3" id="register-prompt">
        Don't have an account? <a href="{{ route('register') }}" id="register-link">Register</a>
    </p>
    </div>
    </div>
</div>

@endsection

<script>
    const token = 'Bearer ' + localStorage.getItem('auth_token')?? null;
    const isAuthenticated = localStorage.getItem('auth_token')??null;

document.addEventListener("DOMContentLoaded",async function () {
    if(isAuthenticated){
        alert('You are logged user.');
        window.location.href = "{{ route('home') }}";
        return;
    }
    const lang = localStorage.getItem('lang') || 'en';
    document.getElementById('langSwitcher').value = lang;
    document.getElementById("login").addEventListener("submit", async function(e) {
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
                localStorage.setItem('user_email', data.data.email);
                //Redirect
                window.location.href = data.redirect || '/';
            }


        } catch (err) {
            console.error("Unexpected error:", err);
            alert("An unexpected error occurred.");
        }
    });
    async function loadLocaleContent() {
        try {
            const response = await fetch('/api/getLogin'); // Your API returns JSON translations
            if (!response.ok) {
                console.error('Failed to load translations');
                return;
            }
            const translations = await response.json();

            // Update text content by IDs with translations from API response
            document.getElementById('login-title').textContent = translations.login || 'Login';
            document.getElementById('label-email').textContent = translations.email_address || 'Email address';
            document.getElementById('label-password').textContent = translations.password || 'Password';
            document.getElementById('label-remember').textContent = translations.remember_me || 'Remember Me';
            document.getElementById('forgot-password-link').textContent = translations.forgot_password || 'Forgot Password?';
            document.getElementById('login-button').textContent = translations.login || 'Login';
            document.getElementById('register-prompt').childNodes[0].textContent = translations.dont_have_account ? translations.dont_have_account + ' ' : "Don't have an account? ";
            document.getElementById('register-link').textContent = translations.register || 'Register';
        } catch (err) {
            console.error('Error loading translations:', err);
        }
    }

    //loadHeaderTranslations();
    await loadLocaleContent();

    document.getElementById('langSwitcher')?.addEventListener('change', async (e) => {
        await loadLocaleContent();
    });

});
</script>
