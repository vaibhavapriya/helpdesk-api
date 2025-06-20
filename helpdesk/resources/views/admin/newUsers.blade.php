@extends('components.layouts.app.admin')

@section('content')
<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card px-1 py-4 col-lg-7">
        <div class="row justify-content-center p-12">  
                <form id="registerForm">
                    @csrf
                    <h2 class="mb-4">New User</h2>
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
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="client">Client</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback" id="status_error"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
        </div>
    </div>
</div>

<script>
    const isAuthenticated = !!localStorage.getItem('auth_token');
    const token = 'Bearer ' + localStorage.getItem('auth_token')?? null;
document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    // Clear previous errors
    ['firstname', 'lastname', 'email', 'role'].forEach(id => {
        const errorDiv = document.getElementById('error-' + id);
        if (errorDiv) errorDiv.textContent = '';
    });

    const formData = {
        firstname: this.firstname.value,
        lastname: this.lastname.value,
        email: this.email.value,
        role: this.role.value
    };

    try {
        const response = await fetch('http://127.0.0.1:8000/api/admin/newUser', {
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