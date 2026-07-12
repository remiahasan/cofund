<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BackingService;
use App\Http\Requests\Backing\StoreBackingRequest;
use App\Http\Requests\Backing\UpdateBackingRequest;
use App\Http\Resources\BackingResource;
use Illuminate\Http\JsonResponse;
use App\Models\Backing;
use App\Models\Campaign;
use App\Models\CampaignTier;

class BackingController extends Controller
{
    public function __construct(
        private readonly BackingService $backingService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Campaign $campaign): JsonResponse
    {
        $backings = $this->backingService->getBackings($campaign);

        return $this->success(
            'Daftar Backing Berhasil Diambil',
            BackingResource::collection($backings),
            [
                'current_page' => $backings->currentPage(),
                'per_page' => $backings->perPage(),
                'last_page' => $backings->lastPage(),
                'total' => $backings->total(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBackingRequest $request): JsonResponse
    {   
        try {
            $backing = $this->backingService->storeBacking(
                $request->validated(),
                auth()->user()
            );

            return $this->success(
                'Backing berhasil dibuat',
                new BackingResource($backing),
                null,
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Backing $backing): JsonResponse
    {
        $backing = $this->backingService->showBacking($backing);

        return $this->success(
            'Backing berhasil diambil',
            new BackingResource($backing)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBackingRequest $request, Backing $backing): JsonResponse
    {
        $backing = $this->backingService->updateBacking(
            $backing,
            $request->validated()
        );

        return $this->success(
            'Backing berhasil diupdate',
            new BackingResource($backing)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Backing $backing): JsonResponse
    {
        $this->backingService->deleteBacking($backing);

        return $this->success('Backing berhasil dihapus');
    }

    public function complete(Backing $backing): JsonResponse
    {
        $backing = $this->backingService->completeBacking($backing);

        return $this->success(
            'Pembayaran berhasil dikonfirmasi',
            new BackingResource($backing)
        );
    }
}

