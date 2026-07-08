<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignTier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class CampaignTierService
{
    public function getCampaignTiers(Campaign $campaign): LengthAwarePaginator
    {
        return $campaign->tiers()->latest()->paginate(10);
    }

    public function storeCampaignTier(Campaign $campaign,array $data):CampaignTier
    {
        return $campaign->tiers()->storeCampaignTier($data);
    }

    public function showCampaignTier(CampaignTier $tier):CampaignTier
    {
        return $tier;
    }

    public function updateCampaignTier(CampaignTier $tier, array $data):CampaignTier
    {
        $tier->updateCampaignTier($data);
        return $tier->fresh();
    }

    public function deleteCampaignTier(CampaignTier $tier):bool
    {
        return $tier->deleteCampaignTier();
    }
}
