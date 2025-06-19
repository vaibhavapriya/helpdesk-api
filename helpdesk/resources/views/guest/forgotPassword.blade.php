@extends('components.layouts.app.client')
@section('content')
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <h2 class="mb-4">Forgot Password</h2>

      <div id="status"></div>

      <form id="forgotPasswordForm">
        @csrf
        <div class="mb-3">
          <label>Email address</label>
          <input type="email" class="form-control" name="email" id="email" required autofocus>
          <div class="text-danger" id="emailError"></div>
        </div>

        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
      </form>
    </div>
  </div>
</div>

<script>
  const token = 'Bearer ' + localStorage.getItem('auth_token')?? null;
  document.getElementById('forgotPasswordForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    // Clear previous status/errors
    document.getElementById('status').innerHTML = '';
    document.getElementById('emailError').textContent = '';

    const email = document.getElementById('email').value;

    try {
      const response = await fetch("http://127.0.0.1:8000/api/forgot-password", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          'Authorization': token,
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ email })
      });

      const result = await response.json();

      if (response.ok) {
        document.getElementById('status').innerHTML =
          `<div class="alert alert-success">${result.message}</div>`;
        document.getElementById('forgotPasswordForm').reset();
      } else {
        if (result.errors?.email) {
          document.getElementById('emailError').textContent = result.errors.email[0];
        } else {
          document.getElementById('status').innerHTML =
            `<div class="alert alert-danger">${result.error || 'Failed to send email.'}</div>`;
        }
      }
    } catch (error) {
      console.error('Fetch error:', error);
      document.getElementById('status').innerHTML =
        `<div class="alert alert-danger">Something went wrong. Please try again later.</div>`;
    }
  });
</script>
@endsection
