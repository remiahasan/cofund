<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use App\Services\AdminOverviewService;
use Illuminate\Http\JsonResponse;

class AdminOverviewController extends Controller
{
    public function __construct(
        protected AdminOverviewService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->overview()
        ]);
    }

    public function fundingChart(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->campaignChart()
        ]);
    }
}