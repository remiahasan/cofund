<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampaignUpdate\StoreCampaignUpdateRequest;
use App\Http\Requests\CampaignUpdate\UpdateCampaignUpdateRequest;
use App\Http\Resources\CampaignUpdateResource;
use App\Models\Campaign;
use App\Models\CampaignUpdate;
use App\Services\CampaignUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignUpdateController extends Controller
{
    public function __construct(
        private readonly CampaignUpdateService $campaignUpdateService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Campaign $campaign): JsonResponse
    {
        $updates = $this->campaignUpdateService->getUpdates($campaign);

        return $this->success(
            'Daftar Update Campaign Berhasil Diambil',
            CampaignUpdateResource::collection($updates),
            [
                'current_page' => $updates->currentPage(),
                'last_page' => $updates->lastPage(),
                'per_page' => $updates->perPage(),
                'total' => $updates->total(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignUpdateRequest $request, Campaign $campaign): JsonResponse
    {
        $update = $this->campaignUpdateService->storeUpdate($campaign, $request->validated());

        return $this->success(
            'Update berhasil dibuat',
            new CampaignUpdateResource($update),
            null,
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign, CampaignUpdate $update): JsonResponse
    {
        
        return $this->success(
            'Detail Update Campaign Berhasil Diambil',
            new CampaignUpdateResource($this->campaignUpdateService->showUpdate($update))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignUpdateRequest $request, Campaign $campaign, CampaignUpdate $update): JsonResponse
    {
        $update = $this->campaignUpdateService->updateUpdate($update, $request->validated());

        return $this->success(
            'Update berhasil diupdate',
            new CampaignUpdateResource($update)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign, CampaignUpdate $update): JsonResponse
    {
        $this->campaignUpdateService->deleteUpdate($update);

        return $this->success('Update berhasil dihapus');
    }
}

