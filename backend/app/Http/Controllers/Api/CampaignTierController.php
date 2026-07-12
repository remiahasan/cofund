<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\StoreCampaignTierRequest;
use App\Http\Requests\Campaign\UpdateCampaignTierRequest;
use App\Http\Resources\CampaignTierResource;
use App\Models\Campaign;
use App\Models\CampaignTier;
use App\Services\CampaignTierService;
use Illuminate\Http\JsonResponse;

class CampaignTierController extends Controller
{
    public function __construct(
        private readonly CampaignTierService $campaignTierService
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        $tiers = $this->campaignTierService->getCampaignTiers();

        return $this->success('Daftar Campaign Tier Berhasil Diambil', CampaignTierResource::collection($tiers));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignTierRequest $request)
    {
        $campaign = Campaign::findOrFail($request->campaign_id);

        $this->authorize('update', $campaign);

        $campaignTier = $this->campaignTierService->storeCampaignTier(
            $campaign,
            $request->validated()
        );

        dd($campaignTier);

        return $this->success('Campaign Tier berhasil dibuat', new CampaignTierResource($campaignTier));
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignTier $campaign_tier): JsonResponse
    {
        $campaign_tier = $this->campaignTierService->showCampaignTier($campaign_tier);

        return $this->success(
            'Detail Campaign Tier Berhasil Diambil',
            new CampaignTierResource($campaign_tier)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignTierRequest $request, CampaignTier $campaign_tier): JsonResponse
    {
        $campaign_tier = $this->campaignTierService->updateCampaignTier($campaign_tier, $request->validated());

        return $this->success(
            'Tier Berhasil Diupdate',
            new CampaignTierResource($campaign_tier)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignTier $campaign_tier): JsonResponse
    {
        $this->campaignTierService->deleteCampaignTier($campaign_tier);

        return $this->success('Tier Berhasil Dihapus');
    }
}

