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
    public function indexAdmin(Request $request)
    {
        $query = $request->query('query');
        $status = $request->query('status');

        $ticketsQuery = Ticket::query();

        // Apply search filter
        if ($query) {
            $ticketsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%')
                ->orWhere('phone', 'like', '%' . $query . '%')
                ->orWhere('title', 'like', '%' . $query . '%'); // Optional
            });
        }

        // Apply status filter
        if ($status) {
            $ticketsQuery->where('status', $status);
        }

        $tickets = $ticketsQuery->latest()->simplePaginate(15);

        return response()->json([
            'success' => true,
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'next_page_url' => $tickets->nextPageUrl(),
                'per_page' => $tickets->perPage(),
                'prev_page_url' => $tickets->previousPageUrl(),
            ]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request)//php artisan storage:link
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
        try {
            $image = $request->file('attachment');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('uploads', $filename, 'public');
            if (!$path) {
                throw new \Exception('File upload failed');
            }

            $extension = $image->getClientOriginalExtension();

            $ticket->images()->create([
                'name' => $filename,
                'link' => $path,
                'filetype' => strtolower($extension),
            ]);
        } catch (\Exception $e) {
            // Optional: Roll back ticket creation or log error
            \Log::error('Attachment upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ticket created, but file upload failed.'
            ], 500);
        }
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

        if ($request->hasFile('attachment')) {
        try {
            $image = $request->file('attachment');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('uploads', $filename, 'public');
            if (!$path) {
                throw new \Exception('File upload failed');
            }

            $extension = $image->getClientOriginalExtension();

            $ticket->images()->create([
                'name' => $filename,
                'link' => $path,
                'filetype' => strtolower($extension),
            ]);
        } catch (\Exception $e) {
            // Optional: Roll back ticket creation or log error
            \Log::error('Attachment upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ticket created, but file upload failed.'
            ], 500);
        }
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

        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Authorize the user to view this ticket
        $this->authorize('view', $ticket);

        // Return the ticket as JSON, including its relationships
        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketRequest $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $this->authorize('view', $ticket);

        $validated = $request->validated();

        // Update ticket fields
        $ticket->title = $validated['title'];
        $ticket->description = $validated['description'];
        $ticket->priority = $validated['priority'];
        $ticket->department = $validated['department'];
        $ticket->save();

        // Handle attachment
        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('uploads', $filename, 'public');
            $extension = $image->getClientOriginalExtension();

            // Check if image exists, update or create
            $existingImage = $ticket->images()->first();
            if ($existingImage) {
                $existingImage->update([
                    'name' => $filename,
                    'link' => $path,
                    'filetype' => strtolower($extension),
                ]);
            } else {
                $ticket->images()->create([
                    'name' => $filename,
                    'link' => $path,
                    'filetype' => strtolower($extension),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully',
            'data' => $ticket->load('images'),
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     $ticket=Ticket::findOrFail($id);
    //     $this->authorize('view', $ticket);
    //     $ticket->delete();
    //     return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully');
    // }
    public function destroy(string $id)
    {
        $ticket = Ticket::with('images')->findOrFail($id);

        $this->authorize('delete', $ticket);

        // Delete associated image files and records
        foreach ($ticket->images as $image) {
            \Storage::disk('public')->delete($image->link);
            $image->delete();
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully'
        ]);
    }

}
