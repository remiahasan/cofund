<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}