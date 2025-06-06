<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::where('requester_id', Auth::id())->latest()->simplePaginate(15);
        return response()->json([
        'success' => true,
        'data' => $tickets->items(),
        'meta' => [
            'current_page' => $tickets->currentPage(),
            'next_page_url' => $tickets->nextPageUrl(),
            //'last_page' => $tickets->lastPage(),
            'per_page' => $tickets->perPage(),
            'prev_page_url' => $tickets->previousPageUrl(),
            // 'total' => $tickets->total(),only for paginate        
        ]
        ]);
    }
    public function indexAdmin()
    {
        $tickets = Ticket::simplePaginate(15);
        return response()->json([
        'success' => true,
        'data' => $tickets->items(),
        'meta' => [
            'current_page' => $tickets->currentPage(),
            'next_page_url' => $tickets->nextPageUrl(),
            //'last_page' => $tickets->lastPage(),
            'per_page' => $tickets->perPage(),
            'prev_page_url' => $tickets->previousPageUrl(),
            // 'total' => $tickets->total(),only for paginate        
        ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request)
    {
        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->priority = $request->priority;
        $ticket->status = 'open';
        $ticket->department = $request->department;
        $ticket->requester_id = auth()->id(); 
        $ticket->save(); // Save first to get the ID

        // Handle attachment
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('uploads', $filename, 'public');

            // Get the file extension/type
            $extension = $image->getClientOriginalExtension();

            // Save in images table using polymorphic relation
            $ticket->images()->create([
                'name' => $filename,
                'link' => $path,
                'filetype' => strtolower($extension),
            ]);
        }

        return response()->json(['success' => true,'message' => 'Ticket created successfully'], 201);
    }
    public function storeAdmin(TicketRequest $request)
    {        
        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->priority = $request->priority;
        $ticket->status = 'open';
        $ticket->department = $request->department;
        $ticket->requester_id = auth()->id(); 
        $ticket->save(); // Save first to get the ID

        // Handle attachment
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('uploads', $filename, 'public');

            // Get the file extension/type
            $extension = $image->getClientOriginalExtension();

            // Save in images table using polymorphic relation
            $ticket->images()->create([
                'name' => $filename,
                'link' => $path,
                'filetype' => strtolower($extension),
            ]);
        }

        return response()->json(['success' => true,'message' => 'Ticket created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the ticket with relationships
        $ticket = Ticket::with(['image', 'replies'])->findOrFail($id);

        // Authorize the user to view this ticket
        $this->authorize('view', $ticket);

        // Return the ticket as JSON, including its relationships
        return response()->json($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketRequest $request, string $id)
    {
        $ticket=Ticket::findOrFail($id);
        // 1. Validate input using `$request->validate()`
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'department' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $this->authorize('view', $ticket);
        // 2. Update the ticket with validated data
        $ticket->title = $validated['title'];
        $ticket->description = $validated['description'];
        $ticket->priority = $validated['priority'];
        $ticket->department = $validated['department'];

                // Handle attachment
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('uploads', $filename, 'public');

            // Get the file extension/type
            $extension = $image->getClientOriginalExtension();

            // Save in images table using polymorphic relation
            $ticket->images()->update([
                'name' => $filename,
                'link' => $path,
                'filetype' => strtolower($extension),
            ]);
        }

        $ticket->save(); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket=Ticket::findOrFail($id);
        $this->authorize('view', $ticket);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully');
    }
}
