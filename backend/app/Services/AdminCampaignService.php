<?php

namespace App\Services;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Collection;
use App\Events\CampaignApproved;
use App\Events\CampaignRejected;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Events\CampaignRefunded;

class AdminCampaignService
{
    public function index(?string $status = null): LengthAwarePaginator
    {
        return Campaign::with([
            'creator',
            'category'
        ])
        ->when(
            $status,
            fn($query) => $query->where('status', $status)
        )
        ->latest()
        ->paginate(10);
    }

    public function reviewQueue()
    {
        return Campaign::with('creator')
        ->where('status','review')
        ->latest()
        ->paginate(10);
    }

    public function approve(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status'=>'active',
        ]);

        event(new CampaignApproved($campaign));

        return $campaign->fresh();
    }

    public function reject(Campaign $campaign,string $reason): Campaign
    {
        $campaign->update([
            'status'=>'draft',
        ]);

        event(
            new CampaignRejected(
                $campaign,
                $reason
            )
        );

        return $campaign->fresh();
    }

    public function forceFail(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'active') {
            throw new \Exception(
                'Campaign tidak aktif.'
            );
        }
        $campaign->update([
            'status'=>'failed'
        ]);
        event(
            new CampaignRefunded($campaign)
        );
        return $campaign->fresh();
    }
    
}
