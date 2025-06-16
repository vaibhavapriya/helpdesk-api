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
                'Accept': 'application/json'
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
