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
        protected CampaignTierService $campaignTierService
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index(Campaign $campaign): JsonResponse
    {
        $tiers = $this->campaignTierService->getCampaignTiers($campaign);

        return response()->json([
            'success' => true,
            'data' => CampaignTierResource::collection($tiers),
            'meta' => [
                'current_page' => $tiers->currentPage(),
                'per_page' => $tiers->perPage(),
                'total' => $tiers->total(),
                'last_page' => $tiers->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignTierRequest $request, Campaign $campaign): JsonResponse
    {
        $tier = $this->campaignTierService->storeCampaignTier($campaign, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tier Berhasil Ditambahkan',
            'data' => new CampaignTierResource($tier),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignTier $tier): JsonResponse
    {
        $tier = $this->campaignTierService->showCampaignTier($tier);

        return response()->json([
            'success' => true,
            'data' => new CampaignTierResource($tier),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignTierRequest $request, CampaignTier $tier): JsonResponse
    {
        $tier = $this->campaignTierService->updateCampaignTier($tier, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tier Berhasil Diupdate',
            'data' => new CampaignTierResource($tier),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignTier $campaignTier): JsonResponse
    {
        $this->campaignTierService->deleteCampaignTier($campaignTier);

        return response()->json([
            'success' => true,
            'message' => 'Tier Berhasil Dihapus',
        ]);
    }
}
