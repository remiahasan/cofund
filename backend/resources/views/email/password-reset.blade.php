<h2>Reset Password</h2>

<p>Hello {{ $user->name }}</p>

<p>
Kami menerima permintaan reset password untuk akun Anda.
Klik link berikut untuk membuat password baru:
</p>

<p>
<a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
</p>

<p>
Link ini akan kedaluwarsa dalam {{ config('auth.passwords.users.expire') }} menit.
Jika Anda tidak meminta reset password, abaikan email ini.
</p>