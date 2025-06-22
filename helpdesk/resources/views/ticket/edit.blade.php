@extends('components.layouts.app.client')

@section('content')
<div class="container">
    <h1 id="page-title">...</h1>

    <div id="error-messages" class="alert alert-danger d-none">
        <ul id="error-list"></ul>
    </div>

    <form id="edit-ticket-form" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label" id="label-title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
            <div class="invalid-feedback" id="title_error"></div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label" id="label-description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            <div class="invalid-feedback" id="description_error"></div>
        </div>

        <div class="mb-3">
            <label for="priority" class="form-label" id="label-priority">Priority</label>
            <select class="form-select" id="priority" name="priority" required>
                <option value="low" id="opt-low">Low</option>
                <option value="medium" id="opt-medium">Medium</option>
                <option value="high" id="opt-high">High</option>
            </select>
            <div class="invalid-feedback" id="priority_error"></div>
        </div>

        <div class="mb-3">
            <label for="department" class="form-label" id="label-department">Department</label>
            <input type="text" class="form-control" id="department" name="department" required>
            <div class="invalid-feedback" id="department_error"></div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label" id="label-status">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="open" id="opt-open">Open</option>
                <option value="closed" id="opt-closed">Closed</option>
            </select>
            <div class="invalid-feedback" id="status_error"></div>
        </div>

        <div class="mb-3">
            <label id="label-attachment">Current Attachment</label><br>
            <img id="current-attachment" src="" alt="Ticket Attachment" style="max-width: 300px;">
        </div>

        <div class="mb-3 form-group">
            <label for="attachment" class="form-label" id="label-change-attachment">Change Attachment</label>
            <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*">
            <div class="invalid-feedback" id="attachment_error"></div>
        </div>

        <button type="submit" class="btn btn-primary" id="btn-update">Update Ticket</button>
    </form>
</div>

<script>
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    const lang = localStorage.getItem('lang') || 'en';
    let editTranslations = {};
    const ticketId = "{{ $ticket->id ?? request()->route('id') }}";
    const apiBase = `http://127.0.0.1:8000/api/tickets/${ticketId}`;

    const form = document.getElementById('edit-ticket-form');
    const errorMessages = document.getElementById('error-messages');
    const errorList = document.getElementById('error-list');
    const currentAttachment = document.getElementById('current-attachment');

    document.addEventListener('DOMContentLoaded', async () => {
        if (!localStorage.getItem('auth_token')) {
            window.location.href = "{{ route('login') }}";
            return;
        }

        await loadEditTranslations();
        await fetchTicket();

        document.getElementById('langSwitcher').value = lang;
        document.getElementById('langSwitcher').addEventListener('change', async (e) => {
            await setLocale(e.target.value);
            await loadEditTranslations();
        });
    });

    async function loadEditTranslations() {
        try {
            const res = await fetch('/api/editticket');
            editTranslations = await res.json();

            document.getElementById('page-title').textContent = editTranslations.page_title;
            document.getElementById('label-title').textContent = editTranslations.title;
            document.getElementById('label-description').textContent = editTranslations.description;
            document.getElementById('label-priority').textContent = editTranslations.priority;
            document.getElementById('label-department').textContent = editTranslations.department;
            document.getElementById('label-status').textContent = editTranslations.status;
            document.getElementById('label-attachment').textContent = editTranslations.attachment;
            document.getElementById('label-change-attachment').textContent = editTranslations.change_attachment;
            document.getElementById('btn-update').textContent = editTranslations.update;

            document.getElementById('opt-low').textContent = editTranslations.low;
            document.getElementById('opt-medium').textContent = editTranslations.medium;
            document.getElementById('opt-high').textContent = editTranslations.high;
            document.getElementById('opt-open').textContent = editTranslations.open;
            document.getElementById('opt-closed').textContent = editTranslations.closed;
        } catch (err) {
            console.error('Failed to load edit translations', err);
        }
    }

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

            document.getElementById('title').value = data.data.title;
            document.getElementById('description').value = data.data.description;
            document.getElementById('priority').value = data.data.priority;
            document.getElementById('department').value = data.data.department;

            // Set status value if exists (default to open)
            document.getElementById('status').value = data.data.status || 'open';

            if (data.filelink) {
                currentAttachment.src = data.filelink.startsWith('http') ? data.filelink : `{{ Storage::url('') }}${data.filelink}`;
                currentAttachment.style.display = 'block';
            } else {
                currentAttachment.style.display = 'none';
            }
        } catch (error) {
            alert('Error loading ticket data');
            console.error(error);
        }
    }


    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        errorMessages.classList.add('d-none');
        errorList.innerHTML = '';

        // Clear previous inline errors including status
        ['title', 'description', 'priority', 'department', 'status', 'attachment'].forEach(field => {
            const errField = document.getElementById(field + '_error');
            if (errField) {
                errField.textContent = '';
                errField.style.display = 'none';
            }
        });

        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(apiBase + '/update', {
                method: 'POST', // Laravel expects POST with _method=PUT for file uploads
                headers: {
                    'Authorization': token,
                    "Accept": "application/json",
                },
                body: formData
            });

            if (response.status === 422) {
                const data = await response.json();
                errorMessages.classList.remove('d-none');

                Object.entries(data.errors).forEach(([field, messages]) => {
                    const errField = document.getElementById(field + '_error');
                    if (errField) {
                        errField.textContent = messages.join(', ');
                        errField.style.display = 'block';
                    }
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
            // Optionally redirect or reload page here
        } catch (error) {
            alert('Unexpected error occurred.');
            console.error(error);
        }
    });

    async function setLocale(locale) {
        try {
            const response = await fetch('/api/locale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ locale })
            });
            if (!response.ok) throw new Error('Failed to set locale');
            await response.json();
        } catch (error) {
            console.error('Error setting locale:', error);
        }
    }
</script>
@endsection

