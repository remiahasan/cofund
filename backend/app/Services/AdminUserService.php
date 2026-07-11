<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminUserService
{
    public function index(): LengthAwarePaginator
    {
        return User::withCount([
                'campaigns',
                'backings',
                'transactions',
            ])
            ->latest()
            ->paginate(10);
    }

    public function show(User $user): User
    {
        return $user->load([
            'campaigns.category',
            'backings.campaign',
            'transactions',
        ]);
    }
}