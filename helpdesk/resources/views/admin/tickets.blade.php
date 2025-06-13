@extends('components.layouts.app.admin')
@section('content')
<div class="container">
    <div class="row justify-content-center row mb-3">
      <div class="col-md-6">
        <input type="text" id="searchInput" class="form-control" placeholder="Search tickets...">
      </div>
      <div class="col-md-3">
        <select id="statusFilter" class="form-select">
          <option value="all">All Statuses</option>
          <option value="Open">Open</option>
          <option value="Closed">Closed</option>
        </select>
      </div>
    </div>

    <h1>Ticket List</h1>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tickets-table-body">
            <!-- JS inserts ticket rows here -->
        </tbody>
    </table>

    <nav>
        <ul class="pagination" id="pagination">
            <!-- JS inserts pagination here -->
        </ul>
    </nav>
</div>
@endsection


<script>

    const token = 'Bearer ' + localStorage.getItem('auth_token');
      // HTML escape helper to prevent XSS
  function escapeHTML(str) {
    return str.replace(/[&<>"']/g, match => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[match]));
  }

  function getStatusBadge(status) {
    switch (status.toLowerCase()) {
      case 'open': return 'primary';
      case 'in progress': return 'warning';
      case 'closed': return 'success';
      case 'pending': return 'secondary';
      default: return 'dark';
    }
  }
 
document.addEventListener("DOMContentLoaded", function () {
    console.log('tickets loading');
    const tableBody = document.getElementById('tickets-table-body');
    const pagination = document.getElementById('pagination');

    if (!localStorage.getItem('auth_token')) {
        alert('You are not logged in. Redirecting to login.');
        window.location.href = '/login';
        return;
    }

    const fetchTickets = async (url) => {
        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token
                }
            });

            if (response.status === 401) {
                alert('Session expired. Please login again.');
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
                return;
            }

            if (!response.ok) {
                const contentType = response.headers.get('content-type');
                const errorMessage = contentType?.includes('application/json')
                    ? (await response.json()).message || 'Fetch error'
                    : 'HTTP error ' + response.status;

                return alert(errorMessage);
            }

            const data = await response.json();
            console.log('tickets:', data);
            renderTickets(data.data);
            renderPagination(data.meta);
        } catch (error) {
            console.error('Unexpected error:', error);
            alert('Failed to fetch tickets.');
        }
    };
    const deleteTicket = async (id) => {
        // if (!confirm('Delete this ticket?')) return;

        try {
            const response = await fetch(`/api/tickets/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token
                }
            });

            if (!response.ok) {
                const err = await response.json();
                alert(err.message || 'Failed to delete ticket');
                return;
            }

            alert('Ticket deleted');
            fetchTickets(); // Refresh table
        } catch (error) {
            console.error('Delete error:', error);
            alert('Unexpected error occurred.');
        }
    };

    const renderTickets = (tickets) => {
        tableBody.innerHTML = tickets.length
            ? tickets.map(ticket => `
                <tr class="ticket-row" data-id="${ticket.id}">
                    <td>${ticket.id}</td>
                    <td>${ticket.title}</td>
                    <td>${capitalize(ticket.priority)}</td>
                    <td> <span class="badge bg-${getStatusBadge(ticket.status)}" onclick="statusview('${ticket.status}')">${capitalize(ticket.status)}</span></td>
                    <td>${ticket.department}</td>
                    <td>
                        <a href="/admin/tickets/${ticket.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm btn-delete">Delete</button>
                    </td>
                </tr>
            `).join('')
            : '<tr><td colspan="6" class="text-center">No tickets found.</td></tr>';

            // Add click listener for entire row
        tableBody.querySelectorAll('.ticket-row').forEach(row => {
            row.addEventListener('click', function () {
                const id = this.dataset.id;
                window.location.href = `/admin/tickets/${id}`;
            });
        });
        // Add event listeners to delete buttons
        tableBody.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', async (event) => {
                event.stopPropagation();
                const id = event.target.closest('tr').dataset.id;
                await deleteTicket(id);
            });
        });
    };

       const renderPagination = (meta) => {
        pagination.innerHTML = '';

        if (meta.prev_page_url) {
            pagination.appendChild(createPageItem('Previous', meta.prev_page_url));
        } else {
            pagination.appendChild(createDisabledPageItem('Previous'));
        }

        // for (let i = 1; i <= meta.last_page; i++) {
        //     const li = document.createElement('li');
        //     li.className = `page-item ${i === meta.current_page ? 'active' : ''}`;
        //     const a = document.createElement('a');
        //     a.className = 'page-link';
        //     a.textContent = i;
        //     a.href = '#';
        //     a.onclick = (e) => {
        //         e.preventDefault();
        //         fetchTickets(`${apiBase}?page=${i}`);
        //     };
        //     li.appendChild(a);
        //     pagination.appendChild(li);
        // }

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
            fetchTickets(url);
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

    const capitalize = (str) => str?.trim() ? str.trim()[0].toUpperCase() + str.trim().slice(1) : '';

  
    fetchTickets('http://127.0.0.1:8000/api/admin/tickets');
});
</script>