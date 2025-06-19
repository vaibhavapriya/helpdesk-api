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
    <nav>
        <ul class="pagination" id="pagination">
            <!-- JS inserts pagination here -->
        </ul>
    </nav>
@endsection

<script>

  document.addEventListener('DOMContentLoaded', () => {
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
    let currentUserQuery = '';
    let currentUserPageUrl = 'http://127.0.0.1:8000/api/admin/profiles';

    function debounce(func, delay) {
      let timer;
      return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
      };
    }

    async function fetchUsers(url = null) {
      const tableBody = document.getElementById('userTableBody');
      const statusDiv = document.getElementById('status');
      const pagination = document.getElementById('pagination');

      const baseUrl = url || 'http://127.0.0.1:8000/api/admin/profiles';
      const queryParams = new URLSearchParams();

      if (currentUserQuery.trim()) {
        queryParams.append('query', currentUserQuery.trim());
      }

      const finalUrl = baseUrl.includes('?')
        ? `${baseUrl}&${queryParams.toString()}`
        : `${baseUrl}?${queryParams.toString()}`;

      try {
        const response = await fetch(finalUrl, {
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
              <td>${user.user_id}</td>
              <td><span class="editable" data-field="firstname">${user.firstname}</span></td>
              <td><span class="editable" data-field="lastname">${user.lastname}</span></td>
              <td><span class="editable" data-field="role">${user.user.role}</span></td>
              <td><span class="editable" data-field="email">${user.email}</span></td>
              <td><span class="editable" data-field="phone">${user.phone}</span></td>
              <td><i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEditing(this, ${user.user_id})"></i></td>
              <td><i class="fa-solid fa-trash text-danger" onclick="deleteUser(${user.user_id})"></i></td>
            `;
            tableBody.appendChild(row);
          });

          renderPagination(result.meta);
          currentUserPageUrl = finalUrl;
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

    function renderPagination(meta) {
      const pagination = document.getElementById('pagination');
      pagination.innerHTML = '';

      if (meta.prev_page_url) {
        pagination.appendChild(createPageItem('Previous', meta.prev_page_url));
      } else {
        pagination.appendChild(createDisabledPageItem('Previous'));
      }

      if (meta.next_page_url) {
        pagination.appendChild(createPageItem('Next', meta.next_page_url));
      } else {
        pagination.appendChild(createDisabledPageItem('Next'));
      }
    }

    function createPageItem(text, url) {
      const li = document.createElement('li');
      li.classList.add('page-item');
      const a = document.createElement('a');
      a.classList.add('page-link');
      a.href = '#';
      a.textContent = text;
      a.onclick = (e) => {
        e.preventDefault();
        fetchUsers(url);
      };
      li.appendChild(a);
      return li;
    }

    function createDisabledPageItem(text) {
      const li = document.createElement('li');
      li.classList.add('page-item', 'disabled');
      li.innerHTML = `<span class="page-link">${text}</span>`;
      return li;
    }

    document.getElementById('searchInput').addEventListener('input', debounce(() => {
      currentUserQuery = document.getElementById('searchInput').value;
      fetchUsers(); // Triggers reload with query
    }, 400));

    fetchUsers(); // Initial load
  });

  // Enable inline editing
  function enableEditing(iconElement, userId) {
    const row = iconElement.closest('tr');
    const editableCells = row.querySelectorAll('.editable');

    editableCells.forEach(cell => {
      const field = cell.dataset.field;
      const value = cell.textContent.trim();

      const input = document.createElement('input');
      input.type = 'text';
      input.className = 'form-control';
      input.name = field;
      input.value = value;
      input.setAttribute('data-original-value', value);

      const td = cell.parentNode;
      td.replaceChild(input, cell);
    });

    iconElement.classList.remove('fa-pen-to-square');
    iconElement.classList.add('fa-save');
    iconElement.onclick = () => saveUserChanges(iconElement, userId);
  }

  // Save inline edits
  function saveUserChanges(iconElement, userId) {
    const row = iconElement.closest('tr');
    const inputs = row.querySelectorAll('input');

    const updatedData = {
      _method: 'PUT'  // Laravel-style method spoofing
    };

    inputs.forEach(input => {
      updatedData[input.name] = input.value;
    });

    const token = 'Bearer ' + localStorage.getItem('auth_token');

    fetch(`http://127.0.0.1:8000/api/profile/${userId}/update`, {
      method: 'POST', // Use POST and spoof PUT
      headers: {
        'Content-Type': 'application/json',
        'Authorization': token,
        'Accept': 'application/json'
      },
      body: JSON.stringify(updatedData)
    })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        inputs.forEach(input => {
          const span = document.createElement('span');
          span.className = 'editable';
          span.dataset.field = input.name;
          span.textContent = input.value;
          input.parentNode.replaceChild(span, input);
        });

        iconElement.classList.remove('fa-save');
        iconElement.classList.add('fa-pen-to-square');
        iconElement.onclick = () => enableEditing(iconElement, userId);
      } else {
        alert('Update failed: ' + (result.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error(error);
      alert('Server error while saving changes.');
    });
  }

</script>



