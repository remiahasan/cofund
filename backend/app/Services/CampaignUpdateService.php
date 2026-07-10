<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignUpdate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Events\CampaignUpdated;

class CampaignUpdateService
{
    public function getUpdates(Campaign $campaign): LengthAwarePaginator
    {
        return $campaign->updates()
            ->latest()
            ->paginate(10);
    }

    public function storeUpdate(Campaign $campaign, array $data): CampaignUpdate
    {
        $update = $campaign->updates()->create($data);

        event(new CampaignUpdated($update));

        return $update;
    }

    public function showUpdate(CampaignUpdate $campaignUpdate): CampaignUpdate
    {
        return $campaignUpdate;
    }

    public function updateUpdate(CampaignUpdate $campaignUpdate, array $data): CampaignUpdate
    {
        $campaignUpdate->update($data);

        return $campaignUpdate->fresh();
    }

    public function deleteUpdate(CampaignUpdate $campaignUpdate): bool
    {
        return $campaignUpdate->delete();
    }
}