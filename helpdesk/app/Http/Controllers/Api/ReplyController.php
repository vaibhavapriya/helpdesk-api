<?php

namespace App\Http\Controllers\Api;

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
    public function store(StorereplyRequest $request, ticket $ticket)
    {
        $reply = Reply::create([
            'replier_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'reply' => $request->reply,
        ]);
        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully');
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
