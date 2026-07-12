<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function registerUser(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'backer',
            'balance' => 0,
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->fresh(),
            'token' => $token,
        ];
    }

    public function loginUser(array $data): array
    {
        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function getAuthenticatedUser(User $user): User
    {
        return $user;
    }

    public function logoutUser(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function sendPasswordResetLink(string $email): array
    {
        $status = Password::sendResetLink(['email' => $email]);

        return [
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ];
    }

    public function resetPassword(array $data): array
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete();
            }
        );

        return [
            'success' => $status === Password::PASSWORD_RESET,
            'message' => __($status),
        ];
    }
}