<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Backing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Campaign;
use Exception;

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

    public function storeTransaction(array $data, User $user): Transaction
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

    public function mockPayment(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {

            if ($transaction->status === 'success') {
                return $transaction;
            }

            $transaction->update([
                'status' => 'success',
            ]);

            $this->processEscrow($transaction);

            return $transaction->fresh([
                'user',
                'backing',
            ]);
        });
    }

    private function processEscrow(Transaction $transaction): void
    {
        $backing = $transaction->backing;

        $backing->update([
            'status' => 'completed',
        ]);

        $backer = $transaction->user;

        if ($backer->balance < $transaction->amount) {
            throw new Exception('Saldo tidak mencukupi');
        }

        $backer->decrement(
            'balance',
            $transaction->amount
        );

        $backing->campaign->increment(
            'collected_amount',
            $transaction->amount
        );
    }

    public function storePaymentTransaction(Backing $backing): Transaction
    {
        return Transaction::create([
            'user_id'      => $backing->user_id,
            'backing_id'   => $backing->id,
            'type'         => 'payment',
            'amount'       => $backing->amount,
            'status'       => 'pending',
            'reference_id' => 'PAY-' . strtoupper(Str::random(12)),
        ]);
    }

    public function disbursementTransaction(Campaign $campaign): void
    {
        DB::transaction(function () use ($campaign) {

            if ($campaign->status !== 'success') {
                return;
            }

            $creator = $campaign->creator;

            $fee = $campaign->collected_amount * 0.05;
            $receive = $campaign->collected_amount - $fee;

            $creator->increment('balance', $receive);

            $backing = $campaign->backings()
                ->where('status', 'completed')
                ->first();

            if (!$backing) {
                return;
            }

            Transaction::create([
                'user_id' => $creator->id,
                'backing_id' => $backing->id,
                'type' => 'disbursement',
                'amount' => $receive,
                'status' => 'success',
                'reference_id' => 'DISB-' . uniqid(),
            ]);

            Transaction::create([
                'user_id' => $creator->id,
                'backing_id' => $backing->id,
                'type' => 'platform_fee',
                'amount' => $fee,
                'status' => 'success',
                'reference_id' => 'FEE-' . uniqid(),
            ]);
        });
    }

    public function refundTransaction(Campaign $campaign): void
    {
        DB::transaction(function () use ($campaign) {

            if ($campaign->status !== 'failed') {
                return;
            }

            $backings = $campaign->backings()
                ->where('status', 'completed')
                ->get();

            foreach ($backings as $backing) {

                $backer = $backing->user;

                $backer->increment('balance', $backing->amount);

                $backing->update([
                    'status' => 'refunded'
                ]);

                Transaction::create([
                    'user_id' => $backer->id,
                    'backing_id' => $backing->id,
                    'type' => 'refund',
                    'amount' => $backing->amount,
                    'status' => 'success',
                    'reference_id' => 'REF-' . uniqid(),
                ]);
            }
        });
    }
}