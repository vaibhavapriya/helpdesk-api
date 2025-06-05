@extends('components.layouts.app.client')

@section('content')
<div class="container  ">
    <div class="row mb-4 align-items-center p-5 bg-light">
        <div class="col-md-8">
            <h2 class="mb-0">User Profile</h2>
        </div>
        <div class="col-md-4 text-md-end text-center">
            <img src="https://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="rounded-circle img-thumbnail" alt="User Avatar" width="100">
        </div>
    </div>

    <form action="##" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" value="{{$profile->id}}">
            </div>

            <div class="col-md-6">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter last name">
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone">
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com">
            </div>

            <div class="col-12">
                <label for="avatar" class="form-label">Profile Picture</label>
                <input type="file" name="avatar" id="avatar" class="form-control">
            </div>

            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Save
                </button>
                <button id="edit" class="btn btn-secondary">
                    <i class="bi bi-arrow-repeat"></i> Edit
                </button>
            </div>
        </div>
    </form>
</div>
<div class="row mb-4 align-items-center bg-light p-5 mt-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-0">Change Password</h2>
        </div>
    </div>
    <form action="##" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password">
            </div>

            <div class="col-md-6">
                <label for="password2" class="form-label">Verify Password</label>
                <input type="password" name="password2" id="password2" class="form-control" placeholder="Verify password">
            </div>

            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Save
                </button>
                <button id="edit" type="reset" class="btn btn-secondary">
                    <i class="bi bi-arrow-repeat"></i> Edit
                </button>
            </div>
        </div>
    </form>
<div>
@endsection
