@extends('components.layouts.app.admin')
@section('content')

<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card px-1 py-4 col-lg-7">
        <form id="ticket-form" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h4 class="title mb-3">New Ticket</h4>
                <p class="information text-muted">Please provide the following information about your query.</p>

                <!-- Requester (searchable dropdown) -->
                <div class="form-group mt-3">
                    <label for="requester_id" class="form-label">Requester</label>
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

                <button type="submit" class="btn btn-primary btn-block confirm-button">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ticket-form');
    const userId = localStorage.getItem('user_id');
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    const requesterSearch = document.getElementById('requester_search');
    const requesterSelect = document.getElementById('requester_id');
    let users = [];

    // Fetch all users on load
    async function fetchUsers() {
        try {
            const response = await fetch('http://127.0.0.1:8000/api/admin/useridemail', {
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                }
            });
            if (!response.ok) throw new Error('Failed to load users');
            data = await response.json(); // expected [{id: 1, email: 'x@y.com'}, ...]
            users=data.data;
            // Populate select initially with all users
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

    // Filter dropdown as user types
    requesterSearch.addEventListener('input', () => {
        const searchTerm = requesterSearch.value.toLowerCase();
        const filtered = users.filter(user => user.email.toLowerCase().includes(searchTerm));
        populateRequesterOptions(filtered);
    });

    // When user selects from dropdown, update input and hide dropdown
    requesterSelect.addEventListener('change', () => {
        const selectedOption = requesterSelect.options[requesterSelect.selectedIndex];
        if (selectedOption) {
            requesterSearch.value = selectedOption.textContent;
            requesterSelect.classList.add('d-none');
        }
    });

    fetchUsers();

    // Form submit handler


    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Clear previous error messages
        ['requester_id', 'title', 'priority', 'department', 'description'].forEach(field => {
            document.getElementById(`${field}_error`).textContent = '';
            const el = document.getElementById(field);
            if(el) el.classList.remove('is-invalid');
        });

        // Validate requester_id manually (make sure requester_id has a value)
        if(!requesterSelect.value) {
            document.getElementById('requester_id_error').textContent = 'Please select a requester.';
            requesterSearch.classList.add('is-invalid');
            return;
        }

        const formData = new FormData(form);
        formData.set('requester_id', requesterSelect.value); // explicitly set requester_id in formData

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
                    // Validation errors
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
            form.reset();
            requesterSelect.classList.add('d-none');
            requesterSearch.value = '';

        } catch (error) {
            console.error('Unexpected error:', error);
            alert('Something went wrong.');
        }
    });
});
</script>
@endsection
