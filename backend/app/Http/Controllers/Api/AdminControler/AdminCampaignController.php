<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminCampaignService;
use App\Models\Campaign;
use App\Http\Resources\AdminCampaignResource;
use App\Http\Requests\Admin\RejectCampaignRequest;

class AdminCampaignController extends Controller
{
    public function __construct(
        protected AdminCampaignService $service
    ){}

    public function review()
    {
        return AdminCampaignResource::collection(
            $this->service->reviewQueue()
        );
    }

    public function show(Campaign $campaign)
    {
        return new AdminCampaignResource(
            $campaign->load([
                'creator',
                'tiers',
                'updates',
                'backings.user'
            ])
        );
    }

    public function approve(Campaign $campaign)
    {
        return new AdminCampaignResource(
            $this->service->approve($campaign)
        );
    }

    public function reject(RejectCampaignRequest $request, Campaign $campaign)
    {
        return new AdminCampaignResource(
            $this->service->reject(
                $campaign,
                $request->validated()['reason']
            )
        );
    }
}
