@extends('components.layouts.app.client')

@section('content')

<div class="container">
    <h1 id="list-title">...</h1>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th id="thead-title">Title</th>
                <th id="thead-priority">Priority</th>
                <th id="thead-status">Status</th>
                <th id="thead-department">Department</th>
                <th id="thead-actions">Actions</th>
            </tr>
        </thead>
        <tbody id="tickets-table-body">
            <!-- JS will populate -->
        </tbody>
    </table>

    <nav>
        <ul class="pagination" id="pagination"></ul>
    </nav>
</div>
@endsection

<script>
    const token = 'Bearer ' + localStorage.getItem('auth_token');
    let translations = {};
    const lang = localStorage.getItem('lang') || 'en';
    document.addEventListener("DOMContentLoaded", async function () {
        // Set default selected value for the language switcher
        document.getElementById('langSwitcher').value = lang;

        if (!localStorage.getItem('auth_token')) {
            alert('Not logged in. Redirecting...');
            window.location.href = "{{ route('login') }}";
            return;
        }

        await loadTranslations();
        fetchTickets();
        document.getElementById('langSwitcher').addEventListener('change', async (e) => {
            const locale = e.target.value;
            await setLocale(locale);
            await loadTranslations();
            fetchTickets(); // refetch tickets if translations affect them
        });
    });
    async function loadTranslations() {
        try {
            const res = await fetch(`/api/tickets`);
            translations = await res.json();

            document.getElementById('list-title').textContent = translations.list_title;
            document.getElementById('thead-title').textContent = translations.title;
            document.getElementById('thead-priority').textContent = translations.priority;
            document.getElementById('thead-status').textContent = translations.status;
            document.getElementById('thead-department').textContent = translations.department;
            document.getElementById('thead-actions').textContent = translations.actions;
        } catch (e) {
            console.error('Failed to load translations', e);
        }
    }

    async function fetchTickets(url = 'http://127.0.0.1:8000/api/mytickets') {
        try {
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token
                }
            });

            const data = await res.json();
            renderTickets(data.data);
            renderPagination(data.meta);
        } catch (e) {
            alert('Error loading tickets.');
        }
    }

    function renderTickets(tickets) {
        const tableBody = document.getElementById('tickets-table-body');
        tableBody.innerHTML = tickets.length ? tickets.map(t => `
            <tr data-id="${t.id}">
                <td>${t.id}</td>
                <td>${t.title}</td>
                <td>${capitalize(t.priority)}</td>
                <td>${capitalize(t.status)}</td>
                <td>${t.department}</td>
                <td>
                    <a href="/tickets/${t.id}/edit" class="btn btn-warning btn-sm">${translations.edit}</a>
                    <button class="btn btn-danger btn-sm btn-delete">${translations.delete}</button>
                </td>
            </tr>
        `).join('') : `<tr><td colspan="6" class="text-center">${translations.no_tickets}</td></tr>`;

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', async e => {
                e.stopPropagation();
                const id = btn.closest('tr').dataset.id;
                await deleteTicket(id);
            });
        });
    }

    async function deleteTicket(id) {
        try {
            const res = await fetch(`/api/tickets/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token
                }
            });

            if (!res.ok) {
                const err = await res.json();
                return alert(err.message || 'Delete failed');
            }

            alert('Deleted');
            fetchTickets();
        } catch (e) {
            alert('Delete failed');
        }
    }

    function renderPagination(meta) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        if (meta.prev_page_url) {
            pagination.appendChild(pageItem(translations.previous, meta.prev_page_url));
        } else {
            pagination.appendChild(disabledPageItem(translations.previous));
        }

        for (let i = 1; i <= meta.last_page; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === meta.current_page ? 'active' : ''}`;
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = i;
            a.onclick = e => {
                e.preventDefault();
                fetchTickets(`http://127.0.0.1:8000/api/mytickets?page=${i}`);
            };
            li.appendChild(a);
            pagination.appendChild(li);
        }

        if (meta.next_page_url) {
            pagination.appendChild(pageItem(translations.next, meta.next_page_url));
        } else {
            pagination.appendChild(disabledPageItem(translations.next));
        }
    }

    function pageItem(text, url) {
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
    }

    function disabledPageItem(text) {
        const li = document.createElement('li');
        li.classList.add('page-item', 'disabled');
        li.innerHTML = `<span class="page-link">${text}</span>`;
        return li;
    }

    function capitalize(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }
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
        if(!response.ok) throw new Error('Failed to set locale');
        await response.json();
        } catch (error) {
        console.error('Error setting locale:', error);
        }
    }


</script>
