@php
    $resetUrl = url('/resetpassword?token=' . $token . '&email=' . urlencode($user->email));
    \Log::info('Password reset URL sent to ' . $user->email . ': ' . $resetUrl);
@endphp
<h2>Reset Your Password</h2>

<p>Hello {{ $user->name }},</p>

<p>You have requested to reset your password. Click the link below to proceed:</p>

<p>
    <a href="{{ url('/resetpassword?token=' . $token . '&email=' . urlencode($user->email)) }}">
        Reset Password
    </a>
</p>

<p>If you did not request a password reset, please ignore this email.</p>
