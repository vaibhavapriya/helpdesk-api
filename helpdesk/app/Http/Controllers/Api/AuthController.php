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

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Create token (with optional ability scopes)
        $token = $user->createToken('auth_token')->plainTextToken;

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
                'user' => $user, // Optional: expose necessary fields only
            ],
        ]);
        
    }

    public function resetP(Request $request)
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

            // You can store this token manually if needed, or just send it in the email
            // Optionally: store for logging/audit
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

            // Send mail with token
            Mail::to($user->email)->send(new ForgotPasswordMail($user, $token));

            return response()->json(['message' => 'Password reset link sent.']);
        } catch (\Exception $e) {
            \Log::error('Password reset mail failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send reset email.'], 500);
        }
    }

    // public function destroy()
    // {
    //     Auth::guard('web')->logout();
    //     return redirect(route('login'));
    // }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function updatePassword(Request $request, string $id)
    {
        // Step 1: Find user by ID
        $user = User::findOrFail($id);

        // Step 2: Authorization
        $this->authorize('view', $user); // or use 'update' if appropriate

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
