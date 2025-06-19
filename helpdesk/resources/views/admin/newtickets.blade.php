@extends('components.layouts.app.admin')
@section('content')

<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card px-1 py-4 col-lg-7">
        <form id="ticket-form" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h4 class="title mb-3">New Ticket</h4>
                <p class="information text-muted">Please provide the following information about your query.</p>

                <!-- User Selection Toggle -->
                <div class="form-group mt-3">
                    <label for="user-toggle" class="form-label">Create ticket for:</label>
                    <select class="form-select" id="user-toggle">
                        <option value="self">Myself</option>
                        <option value="other">Another User</option>
                    </select>
                </div>

                <!-- Requester (Searchable Dropdown) -->
                <div class="form-group mt-3" id="requester-container" style="display: none;">
                    <label for="requester_search" class="form-label">Requester</label>
                    <input type="text" class="form-control" id="requester_search" placeholder="Search user by email">
                    <select class="form-select d-none" id="requester_id" name="requester_id" size="5" style="height: auto;"></select>
                    <div class="invalid-feedback" id="requester_id_error"></div>
                </div>

                <!-- Title -->
                <div class="form-group mt-3">
                    <label for="title" class="form-label">Title</label>
                    <input class="form-control" id="title" name="title" type="text" placeholder="Title">
                    <div class="invalid-feedback" id="title_error"></div>
                </div>

                <!-- Priority -->
                <div class="form-group mt-3">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <div class="invalid-feedback" id="priority_error"></div>
                </div>

                <!-- Department -->
                <div class="form-group mt-3">
                    <label for="department" class="form-label">Department</label>
                    <input class="form-control" type="text" id="department" name="department" placeholder="Department">
                    <div class="invalid-feedback" id="department_error"></div>
                </div>

                <!-- Description -->
                <div class="form-group mt-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Description"></textarea>
                    <div class="invalid-feedback" id="description_error"></div>
                </div>

                <!-- Attachment -->
                <div class="form-group mt-3">
                    <label for="attachment" class="form-label">Attachment</label>
                    <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*">
                </div>

                <div class="text-center mt-3 mb-3">
                    <small>By submitting this ticket you agree to the</small> 
                    <a href="#" class="terms">Terms & Conditions</a>
                </div> 

                <button type="submit" class="btn btn-primary btn-block confirm-button" id="submit-button">Create</button>

                <!-- Loading Spinner -->
                <div id="form-spinner" class="text-center mt-3 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Submitting...</span>
                    </div>
                    <p class="mt-2 text-muted">Submitting ticket...</p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!localStorage.getItem('auth_token')) {
        alert('You are not logged in. Redirecting to login.');
        window.location.href = "{{ route('login') }}";
        return;
    }
    if (localStorage.getItem('user_role') != 'admin') {
        alert('You are admin. Redirecting to client.');
        window.location.href = "{{ route('home') }}";
        return;
    }

    const form = document.getElementById('ticket-form');
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('form-spinner');
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    const userId = localStorage.getItem('user_id');
    const userToggle = document.getElementById('user-toggle');
    const requesterContainer = document.getElementById('requester-container');
    const requesterSearch = document.getElementById('requester_search');
    const requesterSelect = document.getElementById('requester_id');
    let users = [];

    async function fetchUsers() {
        try {
            const response = await fetch('http://127.0.0.1:8000/api/admin/useridemail', {
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                }
            });
            if (!response.ok) throw new Error('Failed to load users');
            const data = await response.json();
            users = data.data;
            populateRequesterOptions(users);
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    }

    function populateRequesterOptions(filteredUsers) {
        requesterSelect.innerHTML = '';
        if (filteredUsers.length === 0) {
            requesterSelect.classList.add('d-none');
            return;
        }
        filteredUsers.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = user.email;
            requesterSelect.appendChild(option);
        });
        requesterSelect.classList.remove('d-none');
    }

    requesterSearch.addEventListener('input', () => {
        const searchTerm = requesterSearch.value.toLowerCase();
        const filtered = users.filter(user => user.email.toLowerCase().includes(searchTerm));
        populateRequesterOptions(filtered);
    });

    requesterSelect.addEventListener('change', () => {
        const selectedOption = requesterSelect.options[requesterSelect.selectedIndex];
        if (selectedOption) {
            requesterSearch.value = selectedOption.textContent;
            requesterSelect.classList.add('d-none');
        }
    });

    document.getElementById('requester_id').value = userId;

    userToggle.addEventListener('change', () => {
        if (userToggle.value === 'self') {
            requesterContainer.style.display = 'none';
            document.getElementById('requester_id').value = userId;
        } else {
            requesterContainer.style.display = 'block';
            document.getElementById('requester_id').value = '';
        }
    });

    fetchUsers();

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Clear previous error messages
        ['requester_id', 'title', 'priority', 'department', 'description'].forEach(field => {
            document.getElementById(`${field}_error`).textContent = '';
            const el = document.getElementById(field);
            if (el) el.classList.remove('is-invalid');
        });

        // Validate requester_id manually
        if (!requesterSelect.value && userToggle.value === 'other') {
            document.getElementById('requester_id_error').textContent = 'Please select a requester.';
            requesterSearch.classList.add('is-invalid');
            return;
        }

        const formData = new FormData(form);
        formData.set('requester_id', userToggle.value === 'self' ? userId : requesterSelect.value);

        // Show spinner and disable submit button
        spinner.classList.remove('d-none');
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';

        try {
            const response = await fetch('http://127.0.0.1:8000/api/admin/tickets', {
                method: 'POST',
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const errorDiv = document.getElementById(`${field}_error`);
                        if (errorDiv) {
                            document.getElementById(field)?.classList.add('is-invalid');
                            errorDiv.textContent = messages[0];
                        }
                    }
                } else if (response.status === 401) {
                    alert('Unauthorized. Please login again.');
                    window.location.href = '/login';
                } else {
                    alert(data.message || 'An error occurred.');
                }
                return;
            }

            alert('Ticket created successfully!');
            window.location.href = "{{ route('atickets') }}";
            form.reset();
            requesterSelect.classList.add('d-none');
            requesterSearch.value = '';
        } catch (error) {
            console.error('Unexpected error:', error);
            alert('Something went wrong.');
        } finally {
            // Hide spinner and re-enable submit button
            spinner.classList.add('d-none');
            submitButton.disabled = false;
            submitButton.textContent = 'Create';
        }
    });
});
</script>

@endsection
