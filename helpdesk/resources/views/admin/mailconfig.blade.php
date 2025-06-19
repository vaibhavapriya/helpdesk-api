@extends('components.layouts.app.admin')
@section('content')
        <main>
        <div id="success" role="alert"></div>
        <div id="error" role="alert"></div>
          <div class="d-flex justify-content-between align-items-center mb-3">

            <h2>Email Configuration</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEmailModal">
              <i class="fas fa-plus"></i> Add Email
            </button>
          </div>

          <!-- Table -->
          <div class="card">
            <div class="card-body table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Active</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="emailTableBody">
                  <!-- Dynamically populated emails will appear here -->
                </tbody>
              </table>
            </div>
          </div>
        </main>
@endsection

<script>
  const token = 'Bearer ' + localStorage.getItem('auth_token');
  async function fetchEmails() {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/admin/mails', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': token
        }
      });
      const result =await response.json();
      const statusDiv = document.getElementById('status');
      const tableBody = document.getElementById('emailTableBody');
      tableBody.innerHTML = '';

      if (result.success === true) {
        const emails = result.data;

        if (emails.length === 0) {
          statusDiv.textContent = "No emails found.";
        } else {
          emails.forEach(email => {
            const row = document.createElement('tr');

            row.innerHTML = `
              <td>
                <input type="radio" name="active_email" ${email.active ? 'checked' : ''} data-id="${email.id}" class="set-active-email">
              </td>
              <td>${email.mail_from_address}</td>
              <td>${email.mail_from_name}</td>
              <td>
                <button class="btn btn-danger btn-sm delete-email" data-id="${email.id}" >
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            `;

            // Attach change event to the radio button
            row.querySelector('.set-active-email').addEventListener('change', async (e) => {
              const id= e.target.getAttribute('data-id');
              try {
                const response = await fetch(`http://127.0.0.1:8000/api/admin/mails/${id}`, {
                  method: 'PATCH',
                  headers: {
                    'Content-Type': 'application/json',
                    'Authorization': token
                  },
                });

                const result = await response.json();

                if (result.success === true) {
                  const successEl = document.getElementById('success');
                  successEl.classList.add('alert', 'alert-success');
                  successEl.textContent = 'Email marked as active.';
                } else {
                  const errorEl = document.getElementById('error');
                  errorEl.classList.add('alert', 'alert-danger');
                  errorEl.textContent = 'Failed to set active email.';
                }
              } catch (error) {
                const errorEl = document.getElementById('error');
                errorEl.classList.add('alert', 'alert-danger');
                errorEl.textContent = 'Error updating active email.';
              }
            });

            row.querySelector('.delete-email').addEventListener('click', async (e) => {
            const emailId = e.target.closest('button').getAttribute('data-id');
            try {
              const deleteResponse = await fetch(`http://127.0.0.1:8000/api/admin/mails/delete/${emailId}`, {
                method: 'DELETE',
                headers: {
                  'Content-Type': 'application/json',
                  'Authorization': token
                }
              });

              const deleteResult = await deleteResponse.json();
              if (deleteResult.status === 'success') {
                const errorEl = document.getElementById('success');
                errorEl.classList.add('alert', 'alert-success');
                errorEl.textContent = 'Email deleted successfully.';
                fetchEmails(); // Re-fetch the email list after deletion
              } else {
                const errorEl = document.getElementById('error');
                errorEl.classList.add('alert', 'alert-danger');
                errorEl.textContent = 'Failed to delete the email.';
              }
            } catch (error) {
              const errorEl = document.getElementById('error');
              errorEl.classList.add('alert', 'alert-danger');
              errorEl.textContent = 'Error deleting the email. Please try again.';
            }
          });
            tableBody.appendChild(row);
          });
        }
      } else {
        const errorEl = document.getElementById('error');
        errorEl.classList.add('alert', 'alert-danger');
        errorEl.textContent = 'Something went wrong';
      }

    } catch (error) {
      const errorEl = document.getElementById('error');
      errorEl.classList.add('alert', 'alert-danger');
      errorEl.textContent = 'Server error. Please try again';
    }
  }

  // Call the function on page load
  fetchEmails();
</script>
</div>

<!-- Modal -->
<div class="modal fade" id="addEmailModal" tabindex="-1" role="dialog" aria-labelledby="addEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" id="mailform">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Email</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Sender Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>App Password</label>
            <input type="password" name="passcode" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
    if (localStorage.getItem('user_role')!='admin') {
        alert('You are admin. Redirecting to client.');
        window.location.href = "{{ route('home') }}";
        return;
    }
  const token = 'Bearer ' + localStorage.getItem('auth_token');

  // Fetch Emails on load
  fetchEmails();

  // Add Email Form Submit
  document.getElementById('mailform').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
      const response = await fetch('http://127.0.0.1:8000/api/admin/mails/post', {
        method: 'POST',
        body: formData,
        headers: {
          'Authorization': token
        }
      });

      const result = await response.text();

      if (result.includes('success')) {
        $('#addEmailModal').modal('hide');
        fetchEmails();
        const successEl = document.getElementById('success');
        successEl.classList.add('alert', 'alert-success');
        successEl.textContent = 'Email configuration added.';
      } else {
        const errorEl = document.getElementById('error');
        errorEl.classList.add('alert', 'alert-danger');
        errorEl.textContent = 'Failed to add email configuration.';
      }
    } catch (error) {
      const errorEl = document.getElementById('error');
      errorEl.classList.add('alert', 'alert-danger');
      errorEl.textContent = 'Error submitting the form. Please try again.';
    }
  });
});
</script>


