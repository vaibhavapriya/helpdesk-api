@extends('components.layouts.app.client')

@section('content')
@include('components.grid-home-client')

    <div class="d-flex justify-content-end mb-3">
        <select id="langSwitcher" class="form-select w-auto">
          <option value="en" selected>English</option>
          <option value="es">Español</option>
        </select>
    </div>
<div class="container mt-5 mb-5 d-flex justify-content-center">

    <div class="card px-1 py-4 col-lg-7">
        <form id="ticket-form" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h4 class="title mb-3">New Ticket</h4>
                <p class="information text-muted">Please provide the following information about your query.</p>
                
                <!-- Email -->
                <div class="form-group mt-3">
                    <label for="email" class="form-label">Email</label>
                    <input class="form-control" id="email" name="email" type="text" placeholder="Email" readonly>
                    <div class="invalid-feedback" id="email_error"></div>
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
document.addEventListener('DOMContentLoaded', async function () {
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    const storedEmail = localStorage.getItem('user_email');
    const lang = localStorage.getItem('lang') || 'en';
    document.getElementById('langSwitcher').value = lang;
    if (storedEmail) document.getElementById('email').value = storedEmail;

    const form = document.getElementById('ticket-form');

    // Load translations
    const loadLocaleContent = async () => {
        const res = await fetch('/api/newticket');
        const t = await res.json();

        document.querySelector('.title').textContent = t.new_ticket;
        document.querySelector('.information').textContent = t.info_text;
        document.querySelector('label[for="email"]').textContent = t.email;
        document.querySelector('label[for="title"]').textContent = t.title;
        document.querySelector('label[for="priority"]').textContent = t.priority;
        document.querySelector('label[for="department"]').textContent = t.department;
        document.querySelector('label[for="description"]').textContent = t.description;
        document.querySelector('label[for="attachment"]').textContent = t.attachment;
        document.querySelector('.terms').textContent = t.terms;
        document.querySelector('.confirm-button').textContent = t.submit;

        // Set placeholders
        document.getElementById('email').placeholder = t.placeholders.email;
        document.getElementById('title').placeholder = t.placeholders.title;
        document.getElementById('department').placeholder = t.placeholders.department;
        document.getElementById('description').placeholder = t.placeholders.description;

        // Set priority options
        document.querySelector('#priority option[value="high"]').textContent = t.high;
        document.querySelector('#priority option[value="medium"]').textContent = t.medium;
        document.querySelector('#priority option[value="low"]').textContent = t.low;
    };

    await loadLocaleContent();

    // Submit form
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        ['title', 'priority', 'department', 'description'].forEach(field => {
            document.getElementById(`${field}_error`).textContent = '';
            document.getElementById(field).classList.remove('is-invalid');
        });

        try {
            const response = await fetch('/api/mytickets', {
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
                } else {
                    alert(data.message || 'Error occurred.');
                }
                return;
            }

            alert('Ticket created!');
            form.reset();

        } catch (err) {
            alert('Something went wrong.');
            console.error(err);
        }
    });

    // Optional: Language switcher
    document.getElementById('langSwitcher')?.addEventListener('change', async (e) => {
        const selectedLocale = e.target.value;
        await fetch('/api/locale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ locale: selectedLocale })
        });
        await loadLocaleContent();
    });
});
</script>

@endsection

    