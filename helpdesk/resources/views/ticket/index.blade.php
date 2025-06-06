@extends('components.layouts.app.client')
@section('content')
    <div class="container">
        <h1>Ticket List</h1>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Department</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tickets-table-body">
                <!-- JS will insert ticket rows here -->
            </tbody>
        </table>

        <!-- Pagination buttons container -->
        <nav>
            <ul class="pagination" id="pagination">
                <!-- JS will insert pagination links here -->
            </ul>
        </nav>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", async function () {
    const tableBody = document.getElementById('tickets-table-body');
    const pagination = document.getElementById('pagination');
    const apiUrlBase = 'http://127.0.0.1:8000/api/tickets';

    async function fetchTickets(url = apiUrlBase) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer 4|M1XdDP0CbSWuzt3Mh7VYe9dJQHn0syJSmvM4dElLcb0f4c23',
                }
            });

            console.log('Status:', response.status);
            console.log('Content-Type:', response.headers.get('content-type'));

            if (!response.ok) {
                // If content-type is JSON, parse error message
                if (response.headers.get('content-type')?.includes('application/json')) {
                    const error = await response.json();
                    alert(error.message || 'Failed to fetch tickets');
                } else {
                    // For HTML error page, just alert status text
                    const text = await response.text();
                    alert('Error: ' + response.status + '\n' + text.substring(0, 200) + '...');
                }
                return;
            }

            const result = await response.json();
            console.log('API response data:', result);
            renderTickets(result.data);
            renderPagination(result.meta);
        } catch (err) {
            console.error(err);
            alert('An unexpected error occurred.');
        }
    }

    function renderTickets(tickets) {
        tableBody.innerHTML = ''; // Clear current rows
        if (!tickets.length) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No tickets found.</td></tr>';
            return;
        }
        tickets.forEach(ticket => {
            const tr = document.createElement('tr');
            tr.style.cursor = "pointer";
            tr.onclick = () => window.location.href = `/tickets/${ticket.id}`;

            tr.innerHTML = `
                <td>${ticket.id}</td>
                <td>${ticket.title}</td>
                <td>${capitalize(ticket.priority)}</td>
                <td>${capitalize(ticket.status)}</td>
                <td>${ticket.department}</td>
                <td>
                    <a href="/tickets/${ticket.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                    <form action="/tickets/${ticket.id}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this ticket?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            `;
            tableBody.appendChild(tr);
        });
    }

    function renderPagination(meta) {
        pagination.innerHTML = '';

        // Previous page
        if (meta.prev_page_url) {
            const liPrev = createPageItem('Previous', meta.prev_page_url);
            pagination.appendChild(liPrev);
        } else {
            pagination.appendChild(createDisabledPageItem('Previous'));
        }

        // Current page indicator
        const liCurrent = document.createElement('li');
        liCurrent.classList.add('page-item', 'active');
        liCurrent.innerHTML = `<span class="page-link">${meta.current_page}</span>`;
        pagination.appendChild(liCurrent);

        // Next page
        if (meta.next_page_url) {
            const liNext = createPageItem('Next', meta.next_page_url);
            pagination.appendChild(liNext);
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
        a.addEventListener('click', (e) => {
            e.preventDefault();
            fetchTickets(url);
        });
        li.appendChild(a);
        return li;
    }

    function createDisabledPageItem(text) {
        const li = document.createElement('li');
        li.classList.add('page-item', 'disabled');
        li.innerHTML = `<span class="page-link">${text}</span>`;
        return li;
    }

    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Initial fetch
    fetchTickets();
});
</script>
@endsection
