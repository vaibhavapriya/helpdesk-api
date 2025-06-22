<!-- resources/views/components/layouts/header-client.blade.php -->

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand me-5" href="{{ route('home') }}">HELPDESK</a>

    <!-- Mobile toggle button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav" id="navbar-items">
        <!-- Always visible -->
        <li class="nav-item">
          <a class="nav-link" href="{{ url('tickets/create') }}">SUBMIT TICKET</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('kb') }}">KNOWLEDGEBASE</a>
        </li>

        <!-- Guest only -->
        <li class="nav-item" id="nav-login">
          <a class="btn btn-primary" href="{{ route('login') }}">LOGIN</a>
        </li>

        <!-- Auth only -->
        <li class="nav-item" id="nav-my-ticket" style="display:none;">
          <a class="nav-link" href="{{ url('tickets') }}">MY TICKET</a>
        </li>
        <li>
          <select id="langSwitcher" class="form-select w-auto">
            <option value="en" selected>English</option>
            <option value="es">Español</option>
          </select>
        </li>
        <li class="nav-item dropdown" id="nav-user-dropdown" style="display:none;">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="far fa-user"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li id="admin-portal" style="display:none;">
              <a class="dropdown-item" href="{{ route('adminhome') }}">
                <i class="fas fa-user-shield me-2"></i> Admin Portal
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item"  href="{{ url('/myProfile') }}"><i class="fas fa-user"></i> My Profile</a></li>
            <li>
              <form id="logout-form" method="POST">
                @csrf
                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<meta name="csrf-token" content="{{ csrf_token() }}">


<script>
document.addEventListener('DOMContentLoaded', () => {
  const token = localStorage.getItem('auth_token');
  const role = localStorage.getItem('user_role'); // expected 'admin' or other

  // Elements
  const navLogin = document.getElementById('nav-login');
  const navMyTicket = document.getElementById('nav-my-ticket');
  const navUserDropdown = document.getElementById('nav-user-dropdown');
  const adminPortal = document.getElementById('admin-portal');
  const logoutForm = document.getElementById('logout-form');

  if (token) {
    // User is logged in
    navLogin.style.display = 'none';
    navMyTicket.style.display = 'block';
    navUserDropdown.style.display = 'block';

    if (role === 'admin') {
      adminPortal.style.display = 'block';
    } else {
      adminPortal.style.display = 'none';
    }
  } else {
    // Guest user
    navLogin.style.display = 'block';
    navMyTicket.style.display = 'none';
    navUserDropdown.style.display = 'none';
  }

  logoutForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      const response = await fetch('/api/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json'
        }
      });

      if (response.ok) {
        // Clear localStorage and redirect to login
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_id');
        localStorage.removeItem('user_role');
        window.location.href = '/login';
      } else {
        alert('Logout failed');
      }
    } catch (error) {
      console.error('Logout error:', error);
      alert('Logout error, see console');
    }
  });
      // Optional: Language switcher
  document.getElementById('langSwitcher')?.addEventListener('change', async (e) => {
        const selectedLocale = e.target.value;
        await fetch('/api/locale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ locale: selectedLocale })
        });
        if (response.ok) {
            // Refresh the page after successful locale update
            window.location.reload();
        } else {
            console.error('Locale update failed:', await response.text());
        }
        
    });
  
});
</script>
