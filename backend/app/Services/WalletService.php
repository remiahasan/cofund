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

    public function topUp(User $user, float $amount): Transaction
    {
        return DB::transaction(function () use ($user, $amount) {
            $user->increment('balance', $amount);

            return Transaction::create([
                'user_id'      => $user->id,
                'type'         => 'topup',
                'amount'       => $amount,
                'status'       => 'success',
                'reference'    => 'TOPUP-' . strtoupper(\Illuminate\Support\Str::random(10)),
            ]);
        });
    }

    public function withdraw(User $user, float $amount): Transaction
    {
        if ($user->balance < $amount) {
            abort(422, 'Saldo tidak mencukupi.');
        }

        return DB::transaction(function () use ($user, $amount) {

            $user->decrement('balance', $amount);

            return Transaction::create([
                'user_id'   => $user->id,
                'type'      => 'withdraw',
                'amount'    => $amount,
                'status'    => 'success',
                'reference' => 'WD-' . strtoupper(\Illuminate\Support\Str::random(10)),
            ]);

        });
    }
}