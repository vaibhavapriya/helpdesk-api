<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorereplyRequest;
use App\Http\Requests\UpdatereplyRequest;
use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $id)
    {
        // Find the ticket by ID
        $ticket = Ticket::findOrFail($id);

        // Manually get the 'reply' input from the request
        $replyContent = request()->input('reply');  

        // Ensure the 'reply' field is provided and not empty
        if (empty($replyContent)) {
            return response()->json(['success' => false, 'message' => 'Reply content is required'], 400);
        }

        // Create the reply record
        $reply = Reply::create([
            'replier_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'reply' => $replyContent,  // Use the manually extracted reply content
        ]);

        // Return the success response
        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
        ]);
    }

    
    /**
     * Display the specified resource.
     */
    public function show(reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatereplyRequest $request, reply $reply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(reply $reply)
    {
        //
    }
}
