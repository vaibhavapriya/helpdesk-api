@extends('components.layouts.app.client')
@section('content')
    <div class="container">
        <h1>Ticket List</h1>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create Ticket</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Department</th>
                    <!-- <th>Requester</th> -->
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'" style="cursor: pointer;">
                        <td>{{ $ticket->id }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>{{ ucfirst($ticket->priority) }}</td>
                        <td>{{ ucfirst($ticket->status) }}</td>
                        <td>{{ $ticket->department }}</td>
                        <!-- <td>{{ $ticket->requester->name }}</td> -->
                        <td>
                            <!-- <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-info">View</a> -->
                            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-6 p-4">
            {{$tickets->links()}}
        </div>
    </div>
@endsection