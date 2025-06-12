<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\User;
use App\Mail\TicketCreatedMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\MailConfig;


class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user(); 
        $tickets = Ticket::where('requester_id', $user->id )->latest()->simplePaginate(15);//->with('replies') 
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
                $q->where('title', 'like', '%' . $query . '%')
                ->orWhere('description', 'like', '%' . $query . '%')
                ->orWhere('id', 'like', '%' . $query . '%')
                ->orWhere('department', 'like', '%' . $query . '%'); // Optional
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

            $ticket->image()->create([
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

        //Send email
        try {
            $userEmail = auth()->user()->email;

            $activeMail = \App\Models\MailConfig::where('active', true)->first();
            config([
                'mail.mailers.smtp.host' => $activeMail->host,
                'mail.mailers.smtp.port' => $activeMail->port,
                'mail.mailers.smtp.encryption' => $activeMail->encryption,
                'mail.mailers.smtp.username' => $activeMail->username,
                'mail.mailers.smtp.password' => $activeMail->password, // decrypt if encrypted
                'mail.from.address' => $activeMail->mail_from_address,
                'mail.from.name' => $activeMail->mail_from_name,
            ]);

            Mail::mailer('smtp')->to($userEmail)->send(new TicketCreatedMail($ticket));

            return response()->json(['success' => true,'message' => 'Ticket created successfully and mail sent'], 201);
        } catch (\Exception $e) {
            \Log::error('Mail failed: ' . $e->getMessage());
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
        $ticket->requester_id = $request->requester_id; 
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

            $ticket->image()->create([
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

        try {
            $requester_id = $request->input('requester_id'); 
            $user=User::findorfail($requester_id);
            $userEmail = $user->email;

            $activeMail = \App\Models\MailConfig::where('active', true)->first();
            config([
                'mail.mailers.smtp.host' => $activeMail->host,
                'mail.mailers.smtp.port' => $activeMail->port,
                'mail.mailers.smtp.encryption' => $activeMail->encryption,
                'mail.mailers.smtp.username' => $activeMail->username,
                'mail.mailers.smtp.password' => $activeMail->password, // decrypt if encrypted
                'mail.from.address' => $activeMail->mail_from_address,
                'mail.from.name' => $activeMail->mail_from_name,
            ]);

            Mail::mailer('smtp')->to($userEmail)->send(new TicketCreatedMail($ticket));

            return response()->json(['success' => true,'message' => 'Ticket created successfully and mail sent'], 201);
        } catch (\Exception $e) {
            \Log::error('Mail failed: ' . $e->getMessage());
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

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $this->authorize('view', $ticket);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string',
            'department' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx',
        ]);

        $ticket->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'department' => $validated['department'],
        ]);

        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $filename, 'public');
            $extension = $image->getClientOriginalExtension();

            $existingImage = $ticket->image()->first();

            if ($existingImage) {
                Storage::disk('public')->delete($existingImage->link);
                $existingImage->update([
                    'name' => $filename,
                    'link' => $path,
                    'filetype' => strtolower($extension),
                ]);
            } else {
                $ticket->image()->create([
                    'name' => $filename,
                    'link' => $path,
                    'filetype' => strtolower($extension),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully',
            //'data' => $ticket->load('image'), // or 'images' depending on your relationship
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::with('image')->findOrFail($id);

        $this->authorize('view', $ticket);

        // Delete associated image files and records
        if ($ticket->image) {
            \Storage::disk('public')->delete($ticket->image->link);
            $ticket->image->delete();
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully'
        ]);
    }

}
