@extends('components.layouts.app.admin')

@section('content')
    <div class="row justify-content-center row mb-3">
      <div class="col-md-6">
        <input type="text" id="searchInput" class="form-control" placeholder="Search Users...">
      </div>
    </div>

<h2 class="mb-4">Users</h2>

<div id="status" class="mb-3 text-muted"></div>

<div class="table-responsive">
  <table id="userTable" class="table table-bordered table-hover table-light">
    <thead class="table-primary">
      <tr>
        <th>User ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Role</th>
        <th>Email</th>
        <th>Phone No</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody id="userTableBody">
      <!-- JavaScript will populate this -->
    </tbody>
  </table>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {
  const token = 'Bearer ' + localStorage.getItem('auth_token');

  async function fetchUsers() {
    const statusDiv = document.getElementById('status');
    const tableBody = document.getElementById('userTableBody');

    try {
      const response = await fetch('http://127.0.0.1:8000/api/admin/profiles', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': token
        }
      });

      const result = await response.json();
      tableBody.innerHTML = '';
      statusDiv.textContent = '';

      if (result.success) {
        const users = result.data;

        if (!users.length) {
          statusDiv.textContent = "No users found.";
          return;
        }

        users.forEach(user => {
          const row = document.createElement('tr');

          row.innerHTML = `
            <td>${user.userid}</td>
            <td><span class="editable" data-field="firstname">${user.firstname}</span></td>
            <td><span class="editable" data-field="lastname">${user.lastname}</span></td>
            <td><span class="editable" data-field="role">${user.role}</span></td>
            <td><span class="editable" data-field="email">${user.email}</span></td>
            <td><span class="editable" data-field="phone">${user.phone}</span></td>
            <td><i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEditing(this, ${user.userid})"></i></td>
            <td><i class="fa-solid fa-trash text-danger" onclick="deleteUser(${user.userid})"></i></td>
          `;

          tableBody.appendChild(row);
        });
      } else {
        statusDiv.className = 'text-danger';
        statusDiv.textContent = result.message || 'Something went wrong.';
      }
    } catch (error) {
      console.error(error);
      statusDiv.className = 'text-danger';
      statusDiv.textContent = 'Server error. Please try again.';
    }
  }

  window.enableEditing = function (icon, userid) {
    const row = icon.closest('tr');
    const fields = row.querySelectorAll('.editable');

    fields.forEach(cell => {
      const field = cell.dataset.field;
      const value = cell.textContent.trim();

      let inputEl;

      if (field === 'role') {
        inputEl = document.createElement('select');
        ['admin', 'agent', 'client'].forEach(role => {
          const option = document.createElement('option');
          option.value = role;
          option.textContent = role.charAt(0).toUpperCase() + role.slice(1);
          if (role === value.toLowerCase()) option.selected = true;
          inputEl.appendChild(option);
        });
      } else {
        inputEl = document.createElement('input');
        inputEl.type = 'text';
        inputEl.value = value;
      }

      inputEl.classList.add('form-control', 'form-control-sm');
      inputEl.setAttribute('name', field);
      cell.innerHTML = '';
      cell.appendChild(inputEl);
    });

    icon.classList.remove('fa-pen-to-square');
    icon.classList.add('fa-floppy-disk');
    icon.onclick = () => saveRow(row, userid);
  }

  window.saveRow = async function (row, userid) {
    const inputs = row.querySelectorAll('input, select');
    const updatedData = { userid };

    inputs.forEach(input => {
      updatedData[input.name] = input.value;
    });

    try {
      const response = await fetch('http://127.0.0.1:8000/api/admin/profiles', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': token
        },
        body: JSON.stringify(updatedData)
      });

      const result = await response.json();

      if (result.success) {
        document.getElementById('status').className = 'text-success';
        document.getElementById('status').textContent = result.message || 'User updated.';
      } else {
        document.getElementById('status').className = 'text-danger';
        document.getElementById('status').textContent = result.message || 'Failed to update user.';
      }
    } catch (error) {
      console.error(error);
      document.getElementById('status').className = 'text-danger';
      document.getElementById('status').textContent = 'Server error.';
    }

    inputs.forEach(input => {
      const span = document.createElement('span');
      span.classList.add('editable');
      span.setAttribute('data-field', input.name);
      span.textContent = input.value;
      input.parentNode.replaceWith(span);
    });

    const icon = row.querySelector('.fa-floppy-disk');
    icon.classList.remove('fa-floppy-disk');
    icon.classList.add('fa-pen-to-square');
    icon.onclick = () => enableEditing(icon, userid);
  }

  window.deleteUser = async function (userid) {
    if (!confirm("Are you sure you want to delete this user?")) return;

    try {
      const response = await fetch(`/api/admin/user/delete/${userid}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'Authorization': token
        }
      });

      const result = await response.json();

      if (result.success) {
        document.getElementById('status').className = 'text-success';
        document.getElementById('status').textContent = 'User deleted successfully.';
        fetchUsers();
      } else {
        document.getElementById('status').className = 'text-danger';
        document.getElementById('status').textContent = result.message || 'Failed to delete user.';
      }
    } catch (error) {
      console.error(error);
      document.getElementById('status').className = 'text-danger';
      document.getElementById('status').textContent = 'Server error.';
    }
  }

  fetchUsers();
});
</script>

