@extends('components.layouts.app.admin')

@section('content')
<div class="container my-4">
    <!-- <h1 class="mb-4">Your Ticket Details </h1> -->

    <div id="ticket-details"></div>

    <!-- Attachment -->
    <div class="mb-4">
        <h3>Attachment:</h3>
        <div id="attachmentContainer"></div>
    </div>

    <h3 class="text-success mt-5">Replies</h3>
    <ul id="replies-list" class="list-unstyled"></ul>

    <form id="reply-form" class="mt-4">
        <div class="d-flex">
            <input type="text" class="form-control me-2" id="reply-input" placeholder="Reply here..." required>
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </form>

    <a href="/tickets" class="btn btn-secondary mt-4">Back to List</a>
</div>
@endsection


<script>
document.addEventListener("DOMContentLoaded", () => {
    const ticketId = "{{ $ticket->id ?? request()->route('id') }}";
    const apiBase = `http://127.0.0.1:8000/api/tickets/${ticketId}`;
    const token = 'Bearer ' + localStorage.getItem('auth_token');

    const ticketContainer = document.getElementById("ticket-details");
    const repliesList = document.getElementById("replies-list");
    const replyForm = document.getElementById("reply-form");
    const replyInput = document.getElementById("reply-input");

    if (!localStorage.getItem('auth_token')) {
        alert('You are not logged in. Redirecting to login.');
        window.location.href = '/login';
        return;
    }

    // Fetch Ticket Details
    const fetchTicket = async () => {
        try {
            const res = await fetch(apiBase, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token
                }
            });

            if (!res.ok) throw new Error("Failed to fetch ticket");

            const data = await res.json();
            const ticket = data.data;
            renderTicket(ticket);
            renderReplies(ticket.replies);
        } catch (err) {
            console.error("Fetch error:", err);
            alert("Failed to load ticket.");
        }
    };

    // Render Ticket Info and Attachment
    function renderTicket(ticket) {
        ticketContainer.innerHTML = `
        <h1 class="display-4 mb-4">${ticket.title}</h1>
        <table class="table table-bordered table-striped fs-5">
            <tbody>
            <tr>
                <th scope="row" class="w-25">Description</th>
                <td>${ticket.description}</td>
            </tr>
            <tr>
                <th scope="row">Status</th>
                <td>
                <span class="badge bg-${getStatusBadgeClass(ticket.status)} fs-6">
                    ${capitalize(ticket.status)}
                </span>
                </td>
            </tr>
            <tr>
                <th scope="row">Priority</th>
                <td>
                <span class="badge bg-${getPriorityBadgeClass(ticket.priority)} fs-6">
                    ${capitalize(ticket.priority)}
                </span>
                </td>
            </tr>
            <tr>
                <th scope="row">Requester</th>
                <td>${ticket.requester?.name || 'Anonymous'}</td>
            </tr>
            </tbody>
        </table>
        `;

        const attachmentContainer = document.getElementById('attachmentContainer');
        if (!ticket.image) {
            attachmentContainer.textContent = "No attachment available.";
        } else {
            attachmentContainer.innerHTML = `<img src="http://127.0.0.1:8000/storage/${ticket.image.link}" 
                alt="Ticket Attachment" style="max-width: 600px; height: auto;">`;
        }
    }

    // Helper functions to get Bootstrap badge colors for status/priority
    function getStatusBadgeClass(status) {
        switch(status?.toLowerCase()) {
            case 'open': return 'success';
            case 'pending': return 'warning';
            case 'closed': return 'secondary';
            default: return 'info';
        }
    }

    function getPriorityBadgeClass(priority) {
        switch(priority?.toLowerCase()) {
            case 'low': return 'info';
            case 'medium': return 'primary';
            case 'high': return 'danger';
            default: return 'secondary';
        }
    }

    // Capitalize first letter utility
    function capitalize(str) {
        return str?.trim() ? str.trim()[0].toUpperCase() + str.trim().slice(1) : '';
    }

    // Render Replies
    function renderReplies(replies, parent = repliesList) {
        parent.innerHTML = '';
        replies.forEach(reply => {
            const li = document.createElement("li");
            li.classList.add("mb-3");
            li.innerHTML = `
                <div class="d-flex">
                    <img src="${reply.user?.avatar_url || '/images/manager_9193795.png'}" width="50" class="rounded-circle me-2">
                    <div>
                        <strong>${reply.user?.name || 'Anonymous'}</strong>
                        <small class="text-muted"> — ${new Date(reply.created_at).toLocaleDateString()}</small>
                        <p>${reply.reply}</p>
                    </div>
                </div>
            `;

            parent.appendChild(li);

            if (reply.children?.length) {
                const ul = document.createElement("ul");
                ul.classList.add("ms-4", "list-unstyled");
                li.appendChild(ul);
                renderReplies(reply.children, ul);
            }
        });
    }

    // Submit Reply
    replyForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        try {
            const res = await fetch(`${apiBase}/comment`, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': token
                },
                body: JSON.stringify({ reply: replyInput.value })
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || "Reply failed");
            }

            replyInput.value = '';
            fetchTicket(); // refresh replies
        } catch (err) {
            console.error("Reply error:", err);
            alert(err.message);
        }
    });

    fetchTicket();
});
</script>
