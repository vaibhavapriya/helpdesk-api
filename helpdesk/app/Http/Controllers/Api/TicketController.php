<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::where('requester_id', Auth::id())->latest()->simplePaginate(15);
        //also send link to next and before pages
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->priority = $request->priority;
        $ticket->status = 'open';  // Default status
        $ticket->department =$request->department;
        $ticket->requester_id = auth()->id(); 
        //image stored in image table
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $filename = time().'_'.$image->getClientOriginalName();
            $path = $image->storeAs('uploads', $filename, 'public');
            // Save file path in the ticket model
            $ticket->filelink = $path;
        }
        $ticket->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', $ticket);//policies
        // Eager load replies
        $ticket->load('replies');
        //also load images
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
                    // 1. Validate input using `$request->validate()`
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'department' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Update the ticket with validated data
        $ticket->title = $validated['title'];
        $ticket->description = $validated['description'];
        $ticket->priority = $validated['priority'];
        $ticket->department = $validated['department'];

        // 3. Handle optional file upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $ticket->attachment = $path;
        }

        $ticket->save(); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully');
    }
}
