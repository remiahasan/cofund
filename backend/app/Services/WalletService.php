<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class WalletService
{
    public function balance(User $user): array
    {
        return [
            'balance' => $user->balance,
        ];
    }

    public function withdraw(User $user, float $amount): Transaction
    {
        if ($user->balance < $amount) {
            abort(422, 'Saldo tidak mencukupi.');
        }

        return DB::transaction(function () use ($user, $amount) {

            $user->decrement('balance', $amount);

            return Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'status' => 'completed',
                'description' => 'Mock withdraw',
            ]);

        });
    }
}