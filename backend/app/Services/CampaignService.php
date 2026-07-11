<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use App\Models\CampaignImage;
use App\Events\CampaignApproved;
use App\Events\CampaignRejected;

class CampaignService
{
    public function getCampaigns(array $filters = []): LengthAwarePaginator
    {
        return Campaign::with([
            'creator',
            'category',
            'images'
        ])
        ->when(
            $filters['search'] ?? null,
            function ($query) use ($filters) {
                $search = $filters['search'];

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
                });
            }
        )
        ->when(
            $filters['category'] ?? null,
            function ($query, $category) {

                $query->where('category_id', $category);

            }
        )

        ->latest()
        ->paginate(10)
        ->withQueryString();
    }

    public function showCampaign(Campaign $campaign): Campaign
    {
        return $campaign->load([
            'creator',
            'category',
            'images',
            'tiers',
            'updates',
        ]);
    }

    public function storeCampaign(array $data, User $user): Campaign
    {
        return DB::transaction(function () use ($data, $user) {
            $campaign = Campaign::create([
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'slug' => Str::slug($data['title']) . '-' . time(),
                'description' => $data['description'],
                'target_amount' => $data['target_amount'],
                'collected_amount' => 0,
                'deadline' => $data['deadline'],
                'video_url' => $data['video_url'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($data['images'] as $index => $image) {
                $path = $image->store('campaigns', 'public');

                CampaignImage::create([
                    'campaign_id' => $campaign->id,
                    'url' => $path,
                    'is_primary' => $index === 0,
                ]);
            }

            return $campaign->load([
                'creator',
                'category',
                'images'
            ]);
        });
    }

    public function updateCampaign(Campaign $campaign, array $data): Campaign
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . $campaign->id;
        }

        $oldStatus = $campaign->status;
        $campaign->update($data);
        $campaign->refresh();

        if (
            $oldStatus !== 'active' &&
            $campaign->status === 'active'
        ) {
            event(new CampaignApproved($campaign));
        }
        if (
            $oldStatus !== 'rejected' &&
            $campaign->status === 'rejected'
        ) {
            event(new CampaignRejected($campaign, $reason));
        }

        return $campaign->load([
            'creator',
            'category',
            'images'
        ]);
    }

    public function deleteCampaign(Campaign $campaign): bool
    {
        return DB::transaction(function() use ($campaign) {
            foreach ($campaign->images as $image){
                Storage::disk('public')->delete($image->url);
            }

            return $campaign->delete();
        });
    }
}