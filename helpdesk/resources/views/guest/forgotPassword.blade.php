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
  const isAuthenticated = !!localStorage.getItem('auth_token');
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
