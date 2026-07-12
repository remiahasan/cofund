<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminOverviewResource;
use App\Services\AdminOverviewService;
use Illuminate\Http\JsonResponse;

class AdminOverviewController extends Controller
{
    public function __construct(
        private readonly AdminOverviewService $service
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(
            'Overview admin berhasil diambil',
            new AdminOverviewResource($this->service->overview())
        );
    }

    public function fundingChart(): JsonResponse
    {
        return $this->success(
            'Chart pendanaan admin berhasil diambil',
            AdminOverviewResource::collection($this->service->campaignChart())
        );
    }
}