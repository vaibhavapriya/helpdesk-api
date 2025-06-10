<h2>Reset Your Password</h2>

<p>Hello {{ $user->name }},</p>

<p>You have requested to reset your password. Click the link below to proceed:</p>

<p>
    <a href="{{ url('/reset-password?token=' . $token . '&email=' . urlencode($user->email)) }}">
        Reset Password
    </a>
</p>

<p>If you did not request a password reset, please ignore this email.</p>
