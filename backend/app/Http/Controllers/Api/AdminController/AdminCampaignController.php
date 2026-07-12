<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminCampaignService;
use App\Models\Campaign;
use App\Http\Resources\AdminCampaignResource;
use App\Http\Requests\Admin\RejectCampaignRequest;
use Illuminate\Http\JsonResponse;

class AdminCampaignController extends Controller
{
    public function __construct(
        private readonly AdminCampaignService $service
    ){}

    public function index(Request $request): JsonResponse
    {
        $campaigns = $this->service->index($request->status);

        return $this->success(
            'Daftar campaign admin berhasil diambil',
            AdminCampaignResource::collection($campaigns)
        );
    }

    public function review(): JsonResponse
    {
        $campaigns = $this->service->reviewQueue();

        return $this->success(
            'Antrean review campaign berhasil diambil',
            AdminCampaignResource::collection($campaigns)
        );
    }

    public function show(Campaign $campaign): JsonResponse
    {
        $data = new AdminCampaignResource(
            $campaign->load([
                'creator',
                'category',
                'tiers',
                'updates',
                'backings.user',
                'images'
            ])
        );

        return $this->success('Detail campaign admin berhasil diambil', $data);
    }

    public function approve(Campaign $campaign): JsonResponse
    {
        $data = $this->service->approve($campaign);

        return $this->success(
            'Campaign berhasil disetujui',
            new AdminCampaignResource($data)
        );
    }

    public function reject(Request $request, Campaign $campaign): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $data = $this->service->reject(
            $campaign,
            $validated['reason']
        );

        return $this->success(
            'Campaign berhasil ditolak',
            new AdminCampaignResource($data)
        );
    }

    public function forceFail(Campaign $campaign): JsonResponse
    {
        $data = $this->service->forceFail($campaign);

        return $this->success(
            'Campaign berhasil dipaksa gagal',
            new AdminCampaignResource($data)
        );
    }
}

