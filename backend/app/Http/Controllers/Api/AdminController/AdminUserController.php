<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserDetailResource;
use App\Http\Resources\AdminUserResource;
use App\Models\User;
use App\Services\AdminUserService;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AdminUserService $service
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->service->index();

        return $this->success(
            'Daftar user admin berhasil diambil',
            AdminUserResource::collection($users),
            [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        );
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(
            'Detail user admin berhasil diambil',
            new AdminUserDetailResource(
                $this->service->show($user)
            )
        );
    }
}