@extends('components.layouts.app.client')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container">
        <h1>Edit Ticket</h1>
        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $ticket->title }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required>{{ $ticket->description }}</textarea>
            </div>
            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority" name="priority" required>
                    <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" value="{{ $ticket->department }}" required>
            </div>
            <img src="{{ Storage::url($ticket->filelink) }}" alt="Ticket Attachment" style="max-width: 300px;">

            <div class="mb-3 form-group">
                <label for="attachment" class="form-label">Attachment</label>
                <div class="input-group"><input class="form-control" type="file" id="attachment" name="attachment" accept="image/*"></div>
            </div>
            <button type="submit" class="btn btn-primary">Update Ticket</button>
        </form>
    </div>
@endsection