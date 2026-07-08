<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Backing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function getTransaction(): LengthAwarePaginator
    {
        return Transaction::with([
            'user',
            'backing'
        ])
        ->latest()
        ->paginate(10);
    }

    public function showTransaction(Transaction $transaction): Transaction
    {
        return $transaction->load([
            'user',
            'backing',
        ]);
    }

    public function createTransaction(array $data, User $user): Transaction
    {
        return DB::transaction(function () use ($data, $user) {

            return Transaction::create([
                'user_id' => $user->id,
                'backing_id' => $data['backing_id'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'status' => 'pending',
                'reference_id' => Str::uuid(),
            ]);

        });
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);

        return $transaction->fresh();
    }

    public function deleteTransaction(Transaction $transaction): bool
    {
        return $transaction->delete();
    }
}