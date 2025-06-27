@extends('components.layouts.app.client')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4" id="register-title">Register</h2>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <form id="registerForm">
                @csrf

                <div class="mb-3">
                    <label id="label-firstname" for="firstname">First Name</label>
                    <input id="firstname" type="text" class="form-control" name="firstname" required>
                    <div class="text-danger" id="error-firstname"></div>
                </div>

                <div class="mb-3">
                    <label id="label-lastname" for="lastname">Last Name</label>
                    <input id="lastname" type="text" class="form-control" name="lastname" required>
                    <div class="text-danger" id="error-lastname"></div>
                </div>

                <div class="mb-3">
                    <label id="label-email" for="email">Email address</label>
                    <input id="email" type="email" class="form-control" name="email" required>
                    <div class="text-danger" id="error-email"></div>
                </div>

                <div class="mb-3">
                    <label id="label-password" for="password">Password</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                    <div class="text-danger" id="error-password"></div>
                </div>

                <div class="mb-3">
                    <label id="label-password-confirmation" for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary" id="register-button">Register</button>
            </form>

            <p class="mt-3" id="login-prompt">
                Already have an account? <a href="{{ route('login') }}" id="login-link">Login</a>
            </p>
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
        async function loadLocaleContent() {
            try {
                const response = await fetch('/api/getRegister'); // Change to your actual API endpoint
                if (!response.ok) {
                    console.error('Failed to load register translations');
                    return;
                }
                const translations = await response.json();

                document.getElementById('register-title').textContent = translations.register || 'Register';
                document.getElementById('label-firstname').textContent = translations.first_name || 'First Name';
                document.getElementById('label-lastname').textContent = translations.last_name || 'Last Name';
                document.getElementById('label-email').textContent = translations.email_address || 'Email address';
                document.getElementById('label-password').textContent = translations.password || 'Password';
                document.getElementById('label-password-confirmation').textContent = translations.confirm_password || 'Confirm Password';
                document.getElementById('register-button').textContent = translations.register || 'Register';

                const loginPrompt = translations.already_have_account || 'Already have an account?';
                const loginLinkText = translations.login || 'Login';

                // Replace text inside <p> but keep the link
                const loginPromptElement = document.getElementById('login-prompt');
                loginPromptElement.childNodes[0].textContent = loginPrompt + ' ';
                document.getElementById('login-link').textContent = loginLinkText;

            } catch (err) {
                console.error('Error loading register translations:', err);
            }
        }
        //loadHeaderTranslations();
        await loadLocaleContent();
        document.getElementById('langSwitcher')?.addEventListener('change', async (e) => {
            await loadLocaleContent();
        });
    });
    document.getElementById('registerForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        // Clear previous errors
        ['firstname', 'lastname', 'email', 'password'].forEach(id => {
            const errorDiv = document.getElementById('error-' + id);
            if (errorDiv) errorDiv.textContent = '';
        });

        const formData = {
            firstname: this.firstname.value,
            lastname: this.lastname.value,
            email: this.email.value,
            password: this.password.value,
            password_confirmation: this.password_confirmation.value
        };

        try {
            const response = await fetch('http://127.0.0.1:8000/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': token,
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
                alert('Registration successful!');
                window.location.href = "{{ route('login') }}";
            } else {
                if (result.errors) {
                    for (const field in result.errors) {
                        const errorDiv = document.getElementById('error-' + field);
                        if (errorDiv) {
                            errorDiv.textContent = result.errors[field][0];
                        }
                    }
                } else {
                    alert(result.message || 'Registration failed.');
                }
            }
        } catch (error) {
            console.error('Registration error:', error);
            alert('Server error. Try again later.');
        }
    });

</script>
@endsection
