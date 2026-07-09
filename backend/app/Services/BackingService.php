<?php

namespace App\Services;

use App\Models\Backing;
use App\Models\Campaign;
use App\Models\CampaignTier;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
            $backing = Backing::create([
                'user_id'=>$user->id,
                'campaign_id'=>$campaign->id,
                'campaign_tier_id'=>$data['campaign_tier_id'],
                'amount'=>$data['amount'],
                'status'=>'pending',
            ]);

            app(TransactionService::class)->storePaymentTransaction($backing);

            return $backing->load([
                'campaign',
                'tier',
                'user'
            ]);
        });
    }

    public function showBacking(Backing $backing): Backing
    {
        return $backing->load([
            'campaign',
            'tier',
            'user'
        ]);
    }

    public function updateBacking(Backing $backing,array $data): Backing
    {
        return DB::transaction(function () use ($backing,$data){
            $backing->update($data);
            return $backing->fresh([
                'campaign',
                'tier',
                'user'
            ]);
        });
    }

    public function deleteBacking(Backing $backing): bool
    {
        return $backing->delete();
    }

}
