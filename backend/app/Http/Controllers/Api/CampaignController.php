<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService $campaignService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $campaigns = $this->campaignService->getCampaigns(
            $request->only([
                'search',
                'category'
            ])
        );

        return response()->json([
            'success' => true,
            'data' => CampaignResource::collection($campaigns),
            'meta' => [
                'current_page' => $campaigns->currentPage(),
                'last_page' => $campaigns->lastPage(),
                'per_page' => $campaigns->perPage(),
                'total' => $campaigns->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignRequest $request): JsonResponse
    {
        $campaign = $this->campaignService->storeCampaign($request->validated(), auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Campaign berhasil dibuat',
            'data' => new CampaignResource($campaign),
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign): JsonResponse
    {
        $campaign = $this->campaignService->showCampaign($campaign);

        return response()->json([
            'success' => true,
            'data' => new CampaignResource($campaign),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): JsonResponse
    {
        $this->authorize('update',$campaign);

        $campaign = $this->campaignService->updateCampaign($campaign, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Campaign berhasil diupdate',
            'data' => new CampaignResource($campaign),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign): JsonResponse
    {
        $this->authorize('delete',$campaign);

        $this->campaignService->deleteCampaign($campaign);

        return response()->json([
            'success' => true,
            'message' => 'Campaign berhasil dihapus',
        ]);
    }
}
