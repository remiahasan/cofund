<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->registerUser($request->validated());

        return $this->success(
            'Register berhasil.',
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            null,
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginUser($request->validated());

        return $this->success(
            'Login berhasil.',
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ]
        );
    }

    public function getAuthenticated(Request $request): JsonResponse
    {
        return $this->success(
            'Data user berhasil diambil.',
            new UserResource(
                $this->authService->getAuthenticatedUser($request->user())
            )
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logoutUser($request->user());

        return $this->success('Logout berhasil.');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->sendPasswordResetLink($request->email);

        if (!$result['success']) {
            return $this->error($result['message'], null, 400);
        }

        return $this->success($result['message']);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->resetPassword($request->validated());

        if (!$result['success']) {
            return $this->error($result['message'], null, 422);
        }

        return $this->success($result['message']);
    }
}