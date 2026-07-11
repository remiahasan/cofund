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
        protected DashboardService $dashboardService
    ) {}

    public function creator(): JsonResponse
    {
        $dashboard = $this->dashboardService
            ->creatorDashboard(auth()->user());

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $dashboard['summary'],
                'campaigns' => DashboardCreatorResource::collection(
                    $dashboard['campaigns']
                ),
            ],
        ]);
    }

    public function fundingChart(): JsonResponse
    {
        $chart = $this->dashboardService
            ->creatorFundingChart(auth()->user());

        return response()->json([
            'success' => true,
            'data' => DashboardChartResource::collection($chart),
        ]);
    }

    public function backer(): JsonResponse
    {
        $summary = $this->dashboardService
            ->backerSummary(auth()->user());

        return response()->json([
            'success' => true,
            'data' => new DashboardBackerResource($summary),
        ]);
    }
}
