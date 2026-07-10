<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CampaignUpdateController extends Controller
{
    public function __construct(
        protected CampaignUpdateService $campaignUpdateService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Campaign $campaign): JsonResponse
    {
        $updates = $this->campaignUpdateService->getUpdates($campaign);

        return response()->json([
            'success' => true,
            'data' => CampaignUpdateResource::collection($updates),
            'meta' => [
                'current_page' => $updates->currentPage(),
                'last_page' => $updates->lastPage(),
                'per_page' => $updates->perPage(),
                'total' => $updates->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignUpdateRequest $request, Campaign $campaign): JsonResponse
    {
        $update = $this->campaignUpdateService->storeUpdate($campaign, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Update berhasil dibuat',
            'data' => new CampaignUpdateResource($update),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignUpdate $campaignUpdate): JsonResponse
    {

        return response()->json([
            'success' => true,
            'data' => new CampaignUpdateResource($this->campaignUpdateService->showUpdate($campaignUpdate)),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignUpdateRequest $request, CampaignUpdate $campaignUpdate): JsonResponse
    {
        $update = $this->campaignUpdateService->updateUpdate($campaignUpdate, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Update berhasil diupdate',
            'data' => new CampaignUpdateResource($update),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignUpdate $campaignUpdate): JsonResponse
    {
        $this->campaignUpdateService->deleteUpdate($campaignUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Update berhasil dihapus',
        ]);
    }
}
