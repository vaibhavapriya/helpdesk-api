<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;  // Your user API resource
use App\Models\User;
use Illuminate\Http\Request;

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
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        //return redirect(route('dashboard'));
    }

    public function resetP(NewPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect(route('login'))->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    public function forgotP(NewPasswordRequest $request)
    {
        //
    }
    public function destroy()
    {
        Auth::guard('web')->logout();
        return redirect(route('login'));
    }
}
