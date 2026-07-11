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
        protected AdminUserService $service
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->service->index();
        return response()->json([
            'success' => true,
            'data' => AdminUserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new AdminUserDetailResource(
                $this->service->show($user)
            ),
        ]);
    }
}