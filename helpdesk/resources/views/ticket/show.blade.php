@extends('components.layouts.app.client')

@section('content')
<div class="container my-4">
    <h1>Ticket Details (API)</h1>

    <div id="ticket-details"></div>
    <h3 class="text-success mt-5">Replies</h3>
    <ul id="replies-list" class="list-unstyled"></ul>

    <form id="reply-form" class="mt-4">
        <div class="d-flex">
            <input type="text" class="form-control me-2" id="reply-input" placeholder="Reply here..." required>
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </form>

    <a href="{{ route('tickets.index') }}" class="btn btn-secondary mt-4">Back to List</a>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ticketId = "{{ $ticket->id }}";
        const apiBase = `/api/tickets/${ticketId}`;
        const token = '{{ csrf_token() }}'; // optional if using Sanctum or JWT

        const ticketContainer = document.getElementById("ticket-details");
        const repliesList = document.getElementById("replies-list");
        const replyForm = document.getElementById("reply-form");
        const replyInput = document.getElementById("reply-input");

        // Fetch Ticket Details + Replies
        fetch(apiBase)
            .then(res => res.json())
            .then(ticket => {
                renderTicket(ticket);
                renderReplies(ticket.replies);
            })
            .catch(err => console.error("Fetch error:", err));

        // Render Ticket Info
        function renderTicket(ticket) {
            ticketContainer.innerHTML = `
                <div><strong>Title:</strong> ${ticket.title}</div>
                <div><strong>Description:</strong> ${ticket.description}</div>
                <div><strong>Status:</strong> ${ticket.status}</div>
                <div><strong>Priority:</strong> ${ticket.priority}</div>
                <div><strong>Requester:</strong> ${ticket.requester?.name || 'Anonymous'}</div>
                ${ticket.filelink ? `<img src="/storage/${ticket.filelink}" class="img-fluid my-2" style="max-width:300px;">` : ''}
            `;
        }

        // Recursive reply renderer
        function renderReplies(replies, parent = repliesList) {
            replies.forEach(reply => {
                const li = document.createElement("li");
                li.classList.add("mb-3");
                li.innerHTML = `
                    <div class="d-flex">
                        <img src="${reply.user?.avatar_url || '/default-avatar.jpg'}" width="50" class="rounded-circle me-2">
                        <div>
                            <strong>${reply.user?.name || 'Anonymous'}</strong>
                            <small class="text-muted"> — ${new Date(reply.created_at).toLocaleDateString()}</small>
                            <p>${reply.reply}</p>
                        </div>
                    </div>
                `;

                parent.appendChild(li);

                if (reply.children && reply.children.length) {
                    const ul = document.createElement("ul");
                    ul.classList.add("ms-4", "list-unstyled");
                    li.appendChild(ul);
                    renderReplies(reply.children, ul);
                }
            });
        }

        // Submit Reply
        replyForm.addEventListener("submit", e => {
            e.preventDefault();

            fetch(`${apiBase}/replies`, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    reply: replyInput.value
                })
            })
            .then(res => {
                if (!res.ok) throw new Error("Failed to post reply");
                return res.json();
            })
            .then(newReply => {
                repliesList.innerHTML = ''; // Clear and reload all
                return fetch(apiBase).then(r => r.json());
            })
            .then(updatedTicket => {
                renderReplies(updatedTicket.replies);
                replyInput.value = '';
            })
            .catch(err => alert("Reply failed: " + err.message));
        });
    });
</script>
@endpush

<!--  -->