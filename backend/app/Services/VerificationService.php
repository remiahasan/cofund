<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerificationService
{
    /**
     * Verify user email using ID and hash.
     */
    public function verifyUser(int $id, string $hash): array
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return [
                'status' => 'invalid',
                'message' => 'Link verifikasi tidak valid.',
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'status' => 'already_verified',
                'message' => 'Email sudah diverifikasi sebelumnya.',
            ];
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return [
            'status' => 'verified',
            'message' => 'Email berhasil diverifikasi.',
        ];
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        return true;
    }
}
