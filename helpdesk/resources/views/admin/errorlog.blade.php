@extends('components.layouts.app.admin')

@section('content')
    <h2 class="mb-4">Error Logs</h2>

    <div id="status" class="mb-3 text-muted"></div>

    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle" id="ticketTable" style="display: none;">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Error Message</th>
            <th>Method</th>
            <th>Route</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody id="tickets-table-body">
          <!-- Rows will be inserted dynamically -->
        </tbody>
      </table>
    </div>
    <nav>
        <ul class="pagination" id="pagination">
            <!-- JS inserts pagination here -->
        </ul>
    </nav>
<script>
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    const table = document.getElementById('ticketTable');
    const tbody = document.getElementById('tickets-table-body');
    document.addEventListener("DOMContentLoaded", function () {
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
    const tableBody = document.getElementById('tickets-table-body');
    const pagination = document.getElementById('pagination');
        const fetchErrors = async (url) => {
            try{
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': token
                    }
                });
                const json=await response.json();
                const data=json.data;
                renderError(data);
                renderPagination(json.meta);
                console.log(json);
            }catch(error){
                console.error("Failed to fetch error logs:", error);
                document.getElementById("status").textContent = "Failed to load error logs.";
            }
        }
        const renderError=(errors)=>{
            errors.forEach(error => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>#${error.id}</td>
              <td>${error.error_message}</td>
              <td>${error.method}</td>
              <td>${error.route}</td>
              <td>${error.created_at}</td>
            `;
            tbody.appendChild(row);
          });
          table.style.display = 'table';

        }

        const renderPagination = (meta) => {
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
        };
        const createPageItem = (text, url) => {
            const li = document.createElement('li');
            li.classList.add('page-item');
            const a = document.createElement('a');
            a.classList.add('page-link');
            a.href = '#';
            a.textContent = text;
            a.onclick = (e) => {
                e.preventDefault();
                fetchErrors(url);
            };
            li.appendChild(a);
            return li;
        };

        const createDisabledPageItem = (text) => {
            const li = document.createElement('li');
            li.classList.add('page-item', 'disabled');
            li.innerHTML = `<span class="page-link">${text}</span>`;
            return li;
        };
        fetchErrors('http://127.0.0.1:8000/api/admin/errorlogs');
    });
</script>
@endsection 