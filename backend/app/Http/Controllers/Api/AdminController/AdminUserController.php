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

    public function creatorRequests(): JsonResponse
    {
        $users = User::where('creator_request_status', 'pending')->get();

        return $this->success(
            'Daftar pengajuan creator',
            AdminUserResource::collection($users)
        );
    }

    public function toCreator(User $user): JsonResponse
    {
        if ($user->role === 'creator') {
            return $this->error('User sudah menjadi creator', 400);
        }

        if ($user->creator_request_status !== 'pending') {
            return $this->error('User belum mengajukan menjadi creator', 400);
        }

        $user->update([
            'role' => 'creator',
            'creator_request_status' => 'approved'
        ]);

        return $this->success(
            'User berhasil diubah menjadi creator',
            new AdminUserDetailResource($user)
        );
    }

    public function rejectCreator(User $user): JsonResponse
    {
        if ($user->creator_request_status !== 'pending') {
            return $this->error('Tidak ada pengajuan yang perlu ditolak', 400);
        }

        $user->update([
            'creator_request_status' => 'rejected'
        ]);

        return $this->success('Pengajuan creator ditolak');
    }
}