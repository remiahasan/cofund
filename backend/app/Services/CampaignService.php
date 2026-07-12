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

use App\Events\CampaignRefunded;
use App\Jobs\DisburseCampaignJob;
use App\Jobs\RefundBackersJob;

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
            event(new CampaignRejected($campaign, $reason ?? 'No reason provided'));
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

    public function submitToReview(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'draft') {
            abort(400, 'Campaign hanya dapat diajukan jika berstatus draft.');
        }

        $campaign->update(['status' => 'review']);

        return $campaign->fresh();
    }

    public function approveCampaign(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'review') {
            abort(400, 'Campaign hanya dapat disetujui jika berstatus review.');
        }

        $campaign->update(['status' => 'active']);

        event(new CampaignApproved($campaign));

        return $campaign->fresh();
    }

    public function rejectCampaign(Campaign $campaign, string $reason): Campaign
    {
        if ($campaign->status !== 'review') {
            abort(400, 'Campaign hanya dapat ditolak jika berstatus review.');
        }

        $campaign->update(['status' => 'draft']);

        event(new CampaignRejected($campaign, $reason));

        return $campaign->fresh();
    }

    public function markAsSuccess(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'active') {
            abort(400, 'Campaign hanya dapat disukseskan jika berstatus aktif.');
        }

        $campaign->update(['status' => 'success']);

        DisburseCampaignJob::dispatch($campaign);

        return $campaign->fresh();
    }

    public function markAsFailed(Campaign $campaign): Campaign
    {
        if ($campaign->status !== 'active') {
            abort(400, 'Campaign hanya dapat digagalkan jika berstatus aktif.');
        }

        $campaign->update(['status' => 'failed']);

        RefundBackersJob::dispatch($campaign);

        return $campaign->fresh();
    }
}