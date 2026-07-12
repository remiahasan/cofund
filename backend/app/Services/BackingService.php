<?php

namespace App\Services;

use App\Models\Backing;
use App\Models\Campaign;
use App\Models\CampaignTier;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Events\NewBackingCreated;
use App\Services\TransactionService;
use App\Services\NotificationService;

class BackingService
{
    public function getBackings(Campaign $campaign): LengthAwarePaginator
    {
        return $campaign->backings()->with([
            'user',
            'campaignTier'
        ])
            ->latest()
            ->paginate(10);
    }

    public function storeBacking(array $data, User $user): Backing
    {
        return DB::transaction(function () use ($data,$user){
            $campaign = Campaign::findOrFail($data['campaign_id']);

            if($campaign->user_id == $user->id) {
                throw new \Exception('Tidak bisa melakukan backing pada campaign sendiri');
            }

            $amount = $data['amount'] ?? null;
            $campaignTier = null;

            if (isset($data['campaign_tier_id']) && $data['campaign_tier_id']) {
                $campaignTier = $campaign->tiers()->findOrFail($data['campaign_tier_id']);

                if (!$campaignTier->hasRemainingQuota()) {
                    throw new \Exception('Tier sudah habis.');
                }
                $amount = $campaignTier->minimum_amount;
            } else {
                if (!$amount) {
                    throw new \Exception('Nominal backing wajib diisi.');
                }
            }

            if($user->balance < $amount){
                throw new \Exception('Saldo tidak cukup.');
            }

            $backing = Backing::create([
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'campaign_tier_id' => $campaignTier ? $campaignTier->id : null,
                'amount' => $amount,
                'status' => 'completed',
            ]);

            app(TransactionService::class)->storePaymentTransaction($user,$campaign,$backing);
            
            
            if ($campaignTier && !$campaignTier->isUnlimited()) {
                $campaignTier->decrement('remaining_quota');
            }
            
            event(new NewBackingCreated($backing));

            return $backing->load([
                'campaign',
                'campaignTier',
                'user'
            ]);
        });
    }

    public function showBacking(Backing $backing): Backing
    {
        return $backing->load([
            'campaign',
            'campaignTier',
            'user'
        ]);
    }

    public function updateBacking(Backing $backing,array $data): Backing
    {
        return DB::transaction(function () use ($backing,$data){
            $backing->update($data);
            return $backing->fresh([
                'campaign',
                'campaignTier',
                'user'
            ]);
        });
    }

    public function deleteBacking(Backing $backing): bool
    {
        return DB::transaction(function () use ($backing) {
            $campaignTier = $backing->campaignTier;
            if ($campaignTier && !$campaignTier->isUnlimited()) {
                $campaignTier->increment('remaining_quota');
            }
            return $backing->delete();
        });
    }

    public function completeBacking(Backing $backing): Backing
    {
        return DB::transaction(function () use ($backing) {
            $backing->update(['status' => 'completed']); // Adjust status name based on app convention (e.g. success, completed, verified)
            // also trigger necessary updates e.g., campaign collected_amount
            $backing->campaign->increment('collected_amount', $backing->amount);
            
            app(NotificationService::class)->backingConfirmed($backing);

            return $backing->fresh([
                'campaign',
                'campaignTier',
                'user'
            ]);
        });
    }

    public function getUserBackings(User $user): LengthAwarePaginator
    {
        return $user->backings()->with([
            'campaign',
            'campaignTier',
            'user'
        ])
            ->latest()
            ->paginate(10);
    }

}
