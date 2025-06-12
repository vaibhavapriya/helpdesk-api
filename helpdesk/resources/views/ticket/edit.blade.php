@extends('components.layouts.app.client')
@section('content')
<div class="container">
    <h1>Edit Ticket</h1>

    <div id="error-messages" class="alert alert-danger d-none">
        <ul id="error-list"></ul>
    </div>

    <form id="edit-ticket-form" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
            <div class="invalid-feedback" id="title_error"></div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            <div class="invalid-feedback" id="description_error"></div>
        </div>

        <div class="mb-3">
            <label for="priority" class="form-label">Priority</label>
            <select class="form-select" id="priority" name="priority" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
            <div class="invalid-feedback" id="priority_error"></div>
        </div>

        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" id="department" name="department" required>
            <div class="invalid-feedback" id="department_error"></div>
        </div>

        <div class="mb-3">
            <label>Current Attachment</label><br>
            <img id="current-attachment" src="" alt="Ticket Attachment" style="max-width: 300px;">
        </div>

        <div class="mb-3 form-group">
            <label for="attachment" class="form-label">Change Attachment</label>
            <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*">
            <div class="invalid-feedback" id="attachment_error"></div>
        </div>

        <button type="submit" class="btn btn-primary">Update Ticket</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('edit-ticket-form');
    const errorMessages = document.getElementById('error-messages');
    const errorList = document.getElementById('error-list');
    const currentAttachment = document.getElementById('current-attachment');

    // Get token from localStorage
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    const ticketId = "{{ $ticket->id ?? request()->route('id') }}";
    const apiBase = `http://127.0.0.1:8000/api/tickets/${ticketId}`;

    // Fetch ticket data from API to pre-fill form
    async function fetchTicket() {
        try {
            const response = await fetch(apiBase, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token
                }
            });
            if (!response.ok) throw new Error('Failed to fetch ticket');
            const data = await response.json();

            // Populate form fields
            document.getElementById('title').value = data.data.title;
            document.getElementById('description').value = data.data.description;
            document.getElementById('priority').value = data.data.priority;
            document.getElementById('department').value = data.data.department;

            if (data.filelink) {
                currentAttachment.src = data.filelink.startsWith('http') ? data.filelink : `{{ Storage::url('') }}${data.filelink}`;
            } else {
                currentAttachment.style.display = 'none';
            }
        } catch (error) {
            alert('Error loading ticket data');
            console.error(error);
        }
    }

    // Call fetchTicket on page load
    fetchTicket();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        errorMessages.classList.add('d-none');
        errorList.innerHTML = '';

        // Prepare form data
        const formData = new FormData(form);
        formData.append('_method', 'PUT');
        try {
            const response = await fetch(`/api/tickets/${ticketId}/update`, {
                method: 'POST', // or 'PUT'if file not uploaded
                headers: {
                    'Authorization': token,
                    // **DO NOT** set 'Content-Type' when sending FormData — browser sets it automatically
                },
                body: formData
            });

            if (response.status === 422) {
                // Validation errors
                const data = await response.json();
                errorMessages.classList.remove('d-none');
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const errorField = document.getElementById(field + '_error');
                    if (errorField) {
                        errorField.textContent = messages.join(', ');
                        errorField.style.display = 'block';
                    }
                    // Also add to alert box
                    messages.forEach(msg => {
                        const li = document.createElement('li');
                        li.textContent = msg;
                        errorList.appendChild(li);
                    });
                });
                return;
            }

            if (!response.ok) {
                throw new Error('Failed to update ticket');
            }

            alert('Ticket updated successfully!');
            // Optionally redirect or refresh page
            // window.location.href = '/tickets/' + ticketId;
        } catch (error) {
            alert('Unexpected error occurred.');
            console.error(error);
        }
    });
});
</script>
@endsection
