@extends('components.layouts.app.client')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Register</h2>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <form id="registerForm">
                @csrf

                <div class="mb-3">
                    <label>First Name</label>
                    <input type="text" class="form-control" name="firstname" required>
                    <div class="text-danger" id="error-firstname"></div>
                </div>

                <div class="mb-3">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="lastname" required>
                    <div class="text-danger" id="error-lastname"></div>
                </div>

                <div class="mb-3">
                    <label>Email address</label>
                    <input type="email" class="form-control" name="email" required>
                    <div class="text-danger" id="error-email"></div>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" required>
                    <div class="text-danger" id="error-password"></div>
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
