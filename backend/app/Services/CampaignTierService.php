<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignTier;


class CampaignTierService
{
    public function getCampaignTiers()
    {
        return CampaignTier::with('campaign')->get();
    }

    public function storeCampaignTier(Campaign $campaign, array $data): CampaignTier
    {
        if($data['quota'] == 0) {
            $data['quota'] = 0;
            $data['remaining_quota'] = 0;
        } else {
            $data['remaining_quota'] = $data['quota'];
        }

        return $campaign->tiers()->create($data);
    }

    public function showCampaignTier(CampaignTier $campaign_tier):CampaignTier
    {
        return $campaign_tier;
    }

    public function updateCampaignTier(CampaignTier $campaign_tier, array $data):CampaignTier
    {
        $campaign_tier->update($data);
        return $campaign_tier->fresh();
    }

    public function deleteCampaignTier(CampaignTier $tier):bool
    {
        return $tier->delete();
    }
}
