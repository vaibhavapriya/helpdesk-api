@extends('components.layouts.app.admin')
@section('content')
          <div class="row">
            <!-- Example Card 1 -->
            <div class="col-md-4 mb-4">
              <a  href="{{ route('profile') }}" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-user fa-2x text-primary me-3"></i>
                    <h5 class="mb-0">User Profile</h5>
                  </div>
                </div>
              </a>
            </div>

            <!-- Example Card 2 -->
            <div class="col-md-4 mb-4">
              <a href="{{url('/admin/tickets?status=Open')}}" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-ticket-alt fa-2x text-success me-3"></i>
                    <h5 class="mb-0">Open Tickets</h5>
                  </div>
                </div>
              </a>
            </div>

            <!-- Example Card 2
            <div class="col-md-4 mb-4">
              <a href="mytickets" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-ticket-alt fa-2x text-success me-3"></i>
                    <h5 class="mb-0">My Tickets</h5>
                  </div>
                </div>
              </a>
            </div> -->

            <!-- Example Card 2
            <div class="col-md-4 mb-4">
              <a href="mytickets" class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-ticket-alt fa-2x text-success me-3"></i>
                    <h5 class="mb-0">Unreplied tickets</h5>Tickets</h5>
                  </div>
                </div>
              </a>
            </div> -->

            <!-- Example Card 3 -->
            <div class="col-md-4 mb-4">
              <a href="{{ route('errorlog') }}"  class="text-decoration-none text-dark">
                <div class="card shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <i class="fas fa-bug fa-2x text-warning me-3"></i>
                    <h5 class="mb-0">Error logs</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
@endsection
<script>

document.addEventListener('DOMContentLoaded', function () {

    if (!localStorage.getItem('auth_token')) {
        alert('You are not logged in. Redirecting to login.');
        window.location.href = "{{ route('login') }}";
        return;
    }
    if (localStorage.getItem('user_role')!='admin') {
        alert('You are admin. Redirecting to client.');
        window.location.href = "{{ route('home') }}";
        return;
    }
});
</script>