<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\StoreprofileRequest;
use App\Http\Requests\UpdateprofileRequest;
use App\Models\Profile;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $queryParam = $request->query('query');

        $profilesQuery = Profile::with('image')->latest();

        if ($queryParam) {
            $profilesQuery->where(function ($q) use ($queryParam) {
                $q->where('firstname', 'like', '%' . $queryParam . '%')
                ->orWhere('lastname', 'like', '%' . $queryParam . '%')
                ->orWhere('email', 'like', '%' . $queryParam . '%')
                ->orWhere('phone', 'like', '%' . $queryParam . '%');
            });
        }

        $profiles = $profilesQuery->simplePaginate(15);

        return response()->json([
            'success' => true,
            'data' => $profiles->items(),
            'meta' => [
                'current_page' => $profiles->currentPage(),
                'next_page_url' => $profiles->nextPageUrl(),
                'per_page' => $profiles->perPage(),
                'prev_page_url' => $profiles->previousPageUrl(),
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
        $profile = Profile::with('image')->where('user_id', $id)->firstOrFail();$profile = Profile::with(['image'])->findOrFail($id);//user_id
        $this->authorize('view', $profile);
        return response()->json([
        'success' => true,
        'data' => $profile,

        ]);
    }

    /**
     * Display the specified resource using userId.
     */

    public function showByUserID(string $id)
    {
        $user = User::with('image')->findOrFail($id);

        //$this->authorize('view', $user); // Authorize viewing this user

        return response()->json([
            'success' => true,
            'data' => $user
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
    public function update(UpdateprofileRequest $request, string $id)
    {
        // 1. Fetch user
        $user = User::with('profile')->findOrFail($id);
        $profile = $user->profile;
        //$profile = Profile::with('image')->where('user_id', $id)->firstOrFail();

        // 2. Authorize the action (optional: create a 'updateProfile' policy if needed)
        $this->authorize('view', $profile); // or use custom policy logic

        // 3. Update user table
        $user->update([
            'name' => $request->firstname." ".$request->lastname,
            'email' => $request->email,
        ]);

        // 4. Update profile table
        if ($user->profile) {
            $user->profile->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone' => $request->phone,
                'email' => $request->email, // if profile also has email
            ]);
        } else {
            // Optionally create profile if missing
            $user->profile()->create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User and profile updated successfully',
            'data' => $user->fresh('profile')
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUsersIdAndEmail()
    {
        $users = User::select('id', 'email')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

}
