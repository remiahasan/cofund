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
        private readonly CampaignService $campaignService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category', 'status', 'sort']);

        $campaigns = $this->campaignService->getCampaigns(
            $filters,
            $request->user('sanctum')
        );

        return $this->success(
            'Daftar Campaign Berhasil Diambil',
            CampaignResource::collection($campaigns),
            [
                'current_page' => $campaigns->currentPage(),
                'last_page' => $campaigns->lastPage(),
                'per_page' => $campaigns->perPage(),
                'total' => $campaigns->total(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignRequest $request): JsonResponse
    {
        $campaign = $this->campaignService->storeCampaign($request->validated(), auth()->user());

        return $this->success(
            'Campaign berhasil dibuat',
            new CampaignResource($campaign),
            null,
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Campaign $campaign): JsonResponse
    {
        if ($campaign->status !== 'active') {
            $user = $request->user('sanctum');
            $isOwnerOrAdmin = $user && ($user->id === $campaign->user_id || $user->role === 'admin');

            if (!$isOwnerOrAdmin) {
                return $this->error('Campaign tidak ditemukan.', null, 404);
            }
        }

        $campaign = $this->campaignService->showCampaign($campaign);

        return $this->success(
            'Campaign berhasil diambil',
            new CampaignResource($campaign)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): JsonResponse
    {
        $this->authorize('update', $campaign);

        $campaign = $this->campaignService->updateCampaign($campaign, $request->validated());

        return $this->success(
            'Campaign berhasil diupdate',
            new CampaignResource($campaign)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign): JsonResponse
    {
        $this->authorize('delete', $campaign);

        $this->campaignService->deleteCampaign($campaign);

        return $this->success('Campaign berhasil dihapus');
    }

    public function toReview(Campaign $campaign): JsonResponse
    {
        $this->authorize('update', $campaign);

        $campaign = $this->campaignService->submitToReview($campaign);

        return $this->success(
            'Campaign berhasil diajukan untuk review',
            new CampaignResource($campaign)
        );
    }
}


