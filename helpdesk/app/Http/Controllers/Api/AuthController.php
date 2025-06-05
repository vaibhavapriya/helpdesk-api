<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
            // $request->authenticate();
            // $request->session()->regenerate();

            // return redirect()->intended(route('dashboard'));
            //     $credentials = $request->only('email', 'password');
            // Validate input
        $request->validated();//only data validated by request

        // Get credentials from the request
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
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
