@extends('components.layouts.app.client')

@section('content')
@include('components.grid-home-client')

<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card px-1 py-4 col-lg-7">
        <form id="ticket-form" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h4 class="title mb-3">New Ticket</h4>
                <p class="information text-muted">Please provide the following information about your query.</p>
                
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

<!-- ✅ Script Section -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ticket-form');
    const token = 'Bearer ' + localStorage.getItem('auth_token');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Clear previous error messages
        ['title', 'priority', 'department', 'description'].forEach(field => {
            document.getElementById(`${field}_error`).textContent = '';
            document.getElementById(field).classList.remove('is-invalid');
        });

        try {
            const response = await fetch('http://127.0.0.1:8000/api/mytickets', {
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

        } catch (error) {
            console.error('Unexpected error:', error);
            alert('Something went wrong.');
        }
    });
});
</script>
@endsection

    