@extends('components.layouts.app.client')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Reset Password</h2>
    <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
    <form id="reset-password-form">
        @csrf

        <input type="hidden" id="token" name="token" value="{{ request()->query('token') }}">

        <div class="mb-3">
            <label>Email address</label>
            <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" required>
            <div id="email-error" class="text-danger"></div>
        </div>

        <div class="mb-3">
            <label>New Password</label>
            <input type="password" id="password" class="form-control" name="password" required>
            <div id="password-error" class="text-danger"></div>
        </div>

        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Reset Password</button>

        <div id="form-message" class="mt-3"></div>
    </form>
    </div>
    </div>
</div>
<script>
    const isAuthenticated = !!localStorage.getItem('auth_token');
    const token = 'Bearer ' + localStorage.getItem('auth_token')?? null;
    const lang = localStorage.getItem('lang') || 'en';
    document.getElementById('langSwitcher').value = lang;
    document.addEventListener('DOMContentLoaded', async function () {
            // Load translations
    const loadLocaleContent = async () => {
        const res = await fetch('/api/newticket');
        const t = await res.json();

        document.querySelector('.title').textContent = t.new_ticket;
        document.querySelector('.information').textContent = t.info_text;
        document.querySelector('label[for="email"]').textContent = t.email;
        document.querySelector('label[for="title"]').textContent = t.title;
        document.querySelector('label[for="priority"]').textContent = t.priority;
        document.querySelector('label[for="department"]').textContent = t.department;
        document.querySelector('label[for="description"]').textContent = t.description;
        document.querySelector('label[for="attachment"]').textContent = t.attachment;
        document.querySelector('.terms').textContent = t.terms;
        document.querySelector('.confirm-button').textContent = t.submit;

        // Set placeholders
        document.getElementById('email').placeholder = t.placeholders.email;
        document.getElementById('title').placeholder = t.placeholders.title;
        document.getElementById('department').placeholder = t.placeholders.department;
        document.getElementById('description').placeholder = t.placeholders.description;

        // Set priority options
        document.querySelector('#priority option[value="high"]').textContent = t.high;
        document.querySelector('#priority option[value="medium"]').textContent = t.medium;
        document.querySelector('#priority option[value="low"]').textContent = t.low;
    };
    //loadHeaderTranslations();
    await loadLocaleContent();
    document.getElementById('langSwitcher')?.addEventListener('change', async (e) => {
        await loadLocaleContent();
    });
    });
document.getElementById('reset-password-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Clear previous errors and message
    ['email-error', 'password-error', 'form-message'].forEach(id => {
        document.getElementById(id).innerText = '';
    });

    const token = document.getElementById('token').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;

    try {
        const response = await fetch('/api/reset-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': token,
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // include if CSRF middleware is enabled on API route
            },
            body: JSON.stringify({ token, email, password, password_confirmation })
        });

        const data = await response.json();

        if (!response.ok) {
            // Show validation errors
            if(data.errors) {
                if(data.errors.email) {
                    document.getElementById('email-error').innerText = data.errors.email[0];
                }
                if(data.errors.password) {
                    document.getElementById('password-error').innerText = data.errors.password[0];
                }
            } else if (data.message) {
                document.getElementById('form-message').innerText = data.message;
            }
        } else {
            // Success
            document.getElementById('form-message').innerText = data.message || 'Password reset successful.';
            // Optionally redirect after delay
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        }
    } catch (error) {
        document.getElementById('form-message').innerText = 'An unexpected error occurred.';
    }
});
</script>


@endsection
