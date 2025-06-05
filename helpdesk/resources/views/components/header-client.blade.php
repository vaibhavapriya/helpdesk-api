<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
      <!-- Brand -->
      <a class="navbar-brand me-5" href="{{ route('home') }}">HELPDESK</a>

      <!-- Toggle button for mobile (optional if you want it collapsible) -->
      <!-- Uncomment below if you want it responsive -->
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
     

      <!-- Menu -->
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
       <ul class="navbar-nav">
        @guest
          <li class="nav-item">
            <a class="nav-link" href="{{ url('tickets/create') }}">SUBMIT TICKET</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('kb') }}">KNOWLEDGEBASE</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-primary" href="{{ route('login') }}">LOGIN</a>
          </li>
        @endguest
        @auth
          <li class="nav-item">
            <a class="nav-link" href="{{ url('tickets/create') }}">SUBMIT TICKET</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('kb') }}">KNOWLEDGEBASE</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('tickets') }}">MY TICKET</a>
          </li>
          <!-- User Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="far fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <!-- admin only -->
                @if(Auth::user()->role === 'admin')
                <li><a class="dropdown-item" href="{{ route('kb') }}"><i class="fas fa-user-shield me-2"></i> Admin Portal</a></li>
                @endif
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="profile"><i class="fas fa-sign-out-alt me-2"></i> My Profile</a></li>
              <li><a class="dropdown-item" href="logout"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
              </form>
            </ul>
          </li>
        @endauth

      </ul>
      </div>

  </div>
</nav>