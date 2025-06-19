@extends('components.layouts.app.admin')
@section('content')
<div class="container">
    <div class="row mb-4 align-items-center p-5 bg-light">
        <div class="col-md-8">
            <h2 class="mb-0">User Profile</h2>
        </div>
        <div class="col-md-4 text-md-end text-center">
            <img id="avatar_display" src="https://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="rounded-circle img-thumbnail" alt="User Avatar" width="100">
        </div>
    </div>

    <form id="profileForm" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">

            {{-- First Name --}}
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <div class="form-control-plaintext d-none" id="firstname_display"></div>
                <input type="text" name="firstname" id="firstname" class="form-control d-none" value="">
            </div>

            {{-- Last Name --}}
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <div class="form-control-plaintext d-none" id="lastname_display"></div>
                <input type="text" name="lastname" id="lastname" class="form-control d-none" value="">
            </div>

            {{-- Phone --}}
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <div class="form-control-plaintext d-none" id="phone_display"></div>
                <input type="text" name="phone" id="phone" class="form-control d-none" value="">
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <div class="form-control-plaintext d-none" id="email_display"></div>
                <input type="email" name="email" id="email" class="form-control d-none" value="">
            </div>

            {{-- Avatar --}}
            <div class="col-12">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="avatar" id="avatar" class="form-control d-none">
            </div>

            <div class="col-12 text-end mt-3">
                <button type="button" id="editBtn" class="btn btn-secondary">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="submit" id="saveBtn" class="btn btn-success d-none">
                    <i class="bi bi-check-circle"></i> Save
                </button>
                <button type="button" id="cancelBtn" class="btn btn-danger d-none">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>

        </div>
    </form>
    <div id="passwordUpdateError" class="alert alert-danger d-none" role="alert"></div>
    <div id="passwordUpdateSuccess" class="alert alert-success d-none" role="alert"></div>
    <form id="passwordForm" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">

            <div class="mb-3">
                <label for="old_Password" class="form-label">Old Password</label>
                <input type="password" class="form-control" id="old_Password" name="old_password" required>
            </div>

            {{-- Password --}}
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <div class="form-control-plaintext d-none" id="new_password"></div>
                <input type="password" class="form-control" name="new_password" required>
                <div class="text-danger" id="error-password"></div>
            </div>

            {{-- Password --}}
            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <div class="form-control-plaintext d-none" id="new_password_confirmation"></div>
                <input type="password" class="form-control" name="new_password_confirmation" required>
            </div>

            <div class="col-12 text-end mt-3">
                <button type="submit" id="cp" class="btn btn-successd">
                    <i class="bi bi-check-circle"></i> Change Password
                </button>

            </div>

        </div>
    </form>
</div>

<script>

    const userId = localStorage.getItem('user_id');
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    const cp= document.getElementById('cp');
    const pform = document.getElementById('passwordForm');
    pform.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(pform);
        formData.append('_method', 'PUT'); // Spoofing PUT request

        async function changePassword() {
            try {
                const response = await fetch(`/api/profile/${userId}/updatePassword`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': token
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Failed to change password');
                const json = await response.json();
                console.log(json);

                alert('Password changed successfully!');
                pform.reset(); // Optional: reset the form

            } catch (error) {
                console.error(error);
                alert('Failed to change password.');
            }
        }

        await changePassword(); // <-- You were missing this
    });
    document.addEventListener('DOMContentLoaded', async () => {
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
        const editBtn = document.getElementById('editBtn');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const form = document.getElementById('profileForm');
        const avatarDisplay = document.getElementById('avatar_display');

        const fields = ['firstname', 'lastname', 'phone', 'email', 'avatar'];
        const originalValues = {};

        function toggleEditMode(editMode) {
            fields.forEach(field => {
                const displayEl = document.getElementById(field + '_display');
                const inputEl = document.getElementById(field);
                if (field === 'avatar') {
                    inputEl.classList.toggle('d-none', !editMode);
                } else {
                    displayEl.classList.toggle('d-none', editMode);
                    inputEl.classList.toggle('d-none', !editMode);
                }
            });
            editBtn.classList.toggle('d-none', editMode);
            saveBtn.classList.toggle('d-none', !editMode);
            cancelBtn.classList.toggle('d-none', !editMode);
        }

        // Fetch profile on load and populate fields
        async function loadProfile() {
            try {
                const response = await fetch(`/api/profile/${userId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': token
                    }
                });
                if (!response.ok) throw new Error('Failed to load profile');
                const json = await response.json();


                const data = json.data;
                console.log(json);

                // Map API keys to form fields
                const fieldMap = {
                    firstname: data.firstname || '',
                    lastname: data.lastname || '',
                    phone: data.phone || '',
                    email: data.email || '',
                };

                // Fill display divs and inputs
                Object.entries(fieldMap).forEach(([field, value]) => {
                    const displayEl = document.getElementById(field + '_display');
                    const inputEl = document.getElementById(field);
                    displayEl.textContent = value;
                    inputEl.value = value;
                    displayEl.classList.remove('d-none'); // show display mode initially
                    inputEl.classList.add('d-none');
                    originalValues[field] = value;
                });

                // Set avatar image URL
                avatarDisplay.src = `http://127.0.0.1:8000/storage/${data.image.link}`|| 'https://ssl.gstatic.com/accounts/ui/avatar_2x.png';

            } catch (error) {
                console.error(error);
                alert('Failed to load profile.');
            }
        }

        // Load profile initially
        await loadProfile();

        editBtn.addEventListener('click', () => {
            toggleEditMode(true);
        });

        cancelBtn.addEventListener('click', () => {
            // Reset inputs to original values
            ['firstname', 'lastname', 'phone', 'email'].forEach(field => {
                const input = document.getElementById(field);
                const display = document.getElementById(field + '_display');
                input.value = originalValues[field];
                display.textContent = originalValues[field];
            });
            toggleEditMode(false);
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            try {
                const response = await fetch(`/api/profile/${userId}/update`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': token
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errData = await response.json();
                    alert('Update failed: ' + (errData.message || 'Unknown error'));
                    return;
                }

                const updatedData = await response.json();

                // Update UI with new values
                ['firstname', 'lastname', 'phone', 'email'].forEach(field => {
                    const input = document.getElementById(field);
                    const display = document.getElementById(field + '_display');
                    display.textContent = input.value;
                    originalValues[field] = input.value;
                });

                // Update avatar preview if file selected
                const avatarInput = document.getElementById('avatar');
                if (avatarInput.files.length > 0) {
                    avatarDisplay.src = URL.createObjectURL(avatarInput.files[0]);
                } else if(updatedData.avatar_url) {
                    avatarDisplay.src = updatedData.avatar_url;
                }

                toggleEditMode(false);
                alert('Profile updated successfully!');
            } catch (error) {
                console.error(error);
                alert('An unexpected error occurred.');
            }
        });
    });
</script>
@endsection
