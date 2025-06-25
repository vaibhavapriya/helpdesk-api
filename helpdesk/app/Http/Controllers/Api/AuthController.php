<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;  // Your user API resource
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use App\Models\MailConfig;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->validated();//only data validated by request

        // // Attempt login
        // if (!Auth::attempt($request->only('email', 'password'))) {
        //     return response()->json([
        //         'message' => 'Invalid credentials.'
        //     ], 401);
        // }

        // /** @var User $user */
        // $user = Auth::user();
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.' ], 401);
        }
        
        $token = $user->createToken('auth_token')->accessToken;

        // Return user resource + token
        return (new UserResource($user))->additional([
            'meta' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }


    public function register(RegisterRequest $request)
    {
        // Password confirmation is usually handled by RegisterRequest validation
        $user = User::create([
            'name' => $request->firstname . ' ' . $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);
        $profile = Profile::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'user_id'=>$user->id,
            'email' => $request->email,
            'phone' => $request->phone ?? '',
        ]);

        // Optional: auto-login the user
        // Auth::login($user);

        return response()->json([
            'success' => true,
            'data' => [
                'msg' => "User registered successfully",
            ],
        ]);
        
    }

    public function resetPassword(Request $request)
    {
        // Step 1: Validate input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Step 2: Attempt password reset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Optional: log user in after reset
                // Auth::login($user);
            }
        );

        // Step 3: Handle response
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function forgotP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->email)->firstOrFail();

            // Generate token and store in password_resets table
            $token = Password::createToken($user);
            dispatch(new \App\Jobs\SendForgotPasswordMail($user, $token));

            // Send mail with token
            //Mail::to($user->email)->send(new ForgotPasswordMail($user, $token));

            return response()->json(['message' => 'Password reset link sent.']);
        } catch (\Exception $e) {
            \Log::error('Password reset mail failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send reset email.'], 500);
        }
    }
    public function resetP(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8', // password_confirmation must match
        ]);

        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update the user's password
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful.'], 200);
        } else {
            return response()->json(['error' => 'Invalid token or email.'], 400);
        }
    }

    // public function destroy()
    // {
    //     Auth::guard('web')->logout();
    //     return redirect(route('login'));
    // }

    public function logout(Request $request)
    {
        //$request->user()->currentAccessToken()->delete();
        $accessToken = $request->user()->token();
        $accessToken->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function updatePassword(Request $request, string $id)
    {
        // Step 1: Find user by ID
        $user = User::findOrFail($id);

        // Step 3: Validate input
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed', // automatically checks new_password_confirmation
        ]);

        // Step 4: Check old password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Old password does not match.',
            ], 422);
        }

        // Step 5: Update to new password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

}
