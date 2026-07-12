<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignImage;
use App\Services\CampaignImageService;
use App\Http\Resources\CampaignImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampaignImageController extends Controller
{
    public function __construct(
        private readonly CampaignImageService $campaignImageService
    ){}

    public function index(Campaign $campaign): JsonResponse
    {
        $images = $this->campaignImageService->getCampaignImage($campaign);

        return $this->success(
            'Daftar gambar campaign berhasil diambil',
            CampaignImageResource::collection($images)
        );
    }

    public function store(Request $request, Campaign $campaign): JsonResponse
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $images = $this->campaignImageService->storeCampaignImage($campaign, $request);

        return $this->success('Gambar campaign berhasil ditambahkan', CampaignImageResource::collection($images));
    }

    public function update(Request $request, Campaign $campaign, CampaignImage $image): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($image->campaign_id !== $campaign->id) {
            return response()->json([
                'message' => 'Gambar tidak ditemukan pada campaign ini.'
            ], 404);
        }

        $image = $this->campaignImageService->updateCampaignImage($image, $request);

        return $this->success(
            'Gambar campaign berhasil diperbarui',
            new CampaignImageResource($image)
        );
    }

    public function destroy(Campaign $campaign, CampaignImage $image): JsonResponse
    {
        $image = $campaign->images()->findOrFail($image->id);

        $this->campaignImageService->deleteCampaignImage($image);

        return $this->success('Gambar campaign berhasil dihapus');
    }

    public function setPrimary(Request $request, Campaign $campaign, CampaignImage $image): JsonResponse
    {
        $image = $this->campaignImageService->setPrimary($campaign, $image);

        return $this->success(
            'Gambar campaign berhasil dijadikan utama',
            new CampaignImageResource($image)
        );
    }
}

