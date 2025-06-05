<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="UTF-8">
    <!--Setting the viewport to make your website look good on all devices:-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./images/favicon.png" />
    <title>HelpDesk</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/lato-font/3.0.0/css/lato-font.min.css"
    integrity="sha512-rSWTr6dChYCbhpHaT1hg2tf4re2jUxBWTuZbujxKg96+T87KQJriMzBzW5aqcb8jmzBhhNSx4XYGA6/Y+ok1vQ=="
    crossorigin="anonymous"
  />
  <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      crossorigin="anonymous"
      defer
  />

</head>
<body class="hold-transition sidebar-mini sidebar-collapse sidebar-open layout-fixed">

<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a href="#" data-widget="pushmenu" role="button" class="nav-link"><i class="fas fa-bars"></i></a>
        <!--  -->
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
          <!-- <span class="badge badge-warning navbar-badge">15</span> -->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="adminProfile" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Profile
          </a>
          <div class="dropdown-divider"></div>
          <a href="adminProfile" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Password
          </a>
          <div class="dropdown-divider"></div>
          <a href="logout" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Logout
          </a>
      </li>
    </ul>
</nav>
<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-collapse">
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel nav-item  mt-3 pb-3 mb-3 d-flex">
      <a href="adminhome" class="nav-link active">
        <i class="fas fa-house nav-icon"></i>
      </a>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item menu-open">
          <a href="#" class="nav-link active">
            <i class="fa-solid fa-ticket nav-icon"></i>
            <p>
              Tickets
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="adminTicket" class="nav-link">
                <i class="far fa-newspaper nav-icon"></i>
                <p>New Ticket</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="tickets" class="nav-link">
                <!-- <i class="far fa-circle nav-icon"></i> -->
                <i class="fas fa-database nav-icon"></i>
                <p>View Tickets</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="users" class="nav-link active">
            <i class="fas fa-user nav-icon"></i>
            <p>
              User
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="home" class="nav-link active">
          <i class="fas fa-display nav-icon"></i>
            <p>
              User Portal
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="mail" class="nav-link active">
          <i class="fas fa-envelope nav-icon"></i>
            <p>
              Mail
            </p>
          </a>
        </li>
        <li class="nav-item menu-open">
          <a href="#" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Help
              <i class="right fas fa-angle-left nav-icon"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="fas fa-lightbulb nav-icon"></i>
                <p>FAQ,Support</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Contact</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="errorlog" class="nav-link">
            <!-- <i class="nav-icon fas fa-th"></i> -->
            <i class="fas fa-bug nav-icon"></i>
            <p>
              Errorlogs
              <span class="right badge badge-danger">New</span>
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<main>
    @yield('content')
</main>

<footer >
    <hr/>
    <div class='flexc'>Copyright © 2025 . All rights reserved. Powered by Faveo</div>
</footer>
<div id="loadingIndicator"  style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(255,255,255,0.8); z-index:9999; justify-content:center; align-items:center;">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>
<!-- Loading overlay -->
<div id="loadingOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(255,255,255,0.8); z-index:9999; justify-content:center; align-items:center;">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
    function showLoadingAndRedirect(url) {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) overlay.style.display = 'flex';
  window.location.href = url;
}

</script>
</body>
</html>