<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\DashboardCreatorResource;
use App\Http\Resources\DashboardChartResource;
use App\Http\Resources\DashboardBackerResource;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function creator(): JsonResponse
    {
        $dashboard = $this->dashboardService
            ->creatorDashboard(auth()->user());

        return $this->success(
            'Dashboard creator berhasil diambil',
            [
                'summary' => $dashboard['summary'],
                'campaigns' => DashboardCreatorResource::collection(
                    $dashboard['campaigns']
                ),
            ]
        );
    }

    public function fundingChart(): JsonResponse
    {
        $chart = $this->dashboardService
            ->creatorFundingChart(auth()->user());

        return $this->success(
            'Chart pendanaan creator berhasil diambil',
            DashboardChartResource::collection($chart)
        );
    }

    public function backer(): JsonResponse
    {
        $summary = $this->dashboardService
            ->backerSummary(auth()->user());

        return $this->success(
            'Dashboard backer berhasil diambil',
            new DashboardBackerResource($summary)
        );
    }
}

