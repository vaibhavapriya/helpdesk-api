<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreprofileRequest;
use App\Http\Requests\UpdateprofileRequest;
use App\Models\Profile;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profiles = Profile::load(['image'])->latest()->simplePaginate(15);
        return response()->json([
        'success' => true,
        'data' => $profiles->items(),
        'meta' => [
            'current_page' => $profiles->currentPage(),
            'next_page_url' => $profiles->nextPageUrl(),
            //'last_page' => $tickets->lastPage(),
            'per_page' => $profiles->perPage(),
            'prev_page_url' => $profiles->previousPageUrl(),
            // 'total' => $tickets->total(),only for paginate        
        ]
        ]);
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
    public function store(StoreprofileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $profile = User::with(['image'])->findOrFail($id);//user_id
        $this->authorize('view', $ticket);
        return response()->json([
        'success' => true,
        'data' => $profile,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(id $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateprofileRequest $request, profile $profile)
    {
        $this->authorize('view', $profile);
        $profile = Profile::update([
            'firstname'=>$request->firstname,
            'lastname'=>$request->lastname,
            'phone'=>$request->phone,
            'email' => $request->email,
        ]);//with user_id
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(profile $profile)
    {
        //
    }
}
