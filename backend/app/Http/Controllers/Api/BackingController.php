<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BackingService;
use App\Http\Requests\Backing\StoreBackingRequest;
use App\Http\Requests\Backing\UpdateBackingRequest;
use App\Http\Resources\BackingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Backing;
use App\Models\Campaign;

class BackingController extends Controller
{
    public function __construct(protected BackingService $backingService)
    {}
    /**
     * Display a listing of the resource.
     */
    public function index(Campaign $campaign): JsonResponse
    {
        $backings = $this->backingService->getBackings($campaign);

        return response()->json([
            'success' => true,
            'data' => BackingResource::collection($backings),
            'meta' => [
                'current_page' => $backings->currentPage(),
                'per_page' => $backings->perPage(),
                'last_page' => $backings->lastPage(),
                'total' => $backings->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBackingRequest $request): JsonResponse
    {
        $backing = $this->backingService->storeBacking(
            $request->validated(),
            auth()->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Backing berhasil dibuat',
            'data' => new BackingResource($backing),
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Backing $backing): JsonResponse
    {
        $backing = $this->backingService->showBacking($backing);

        return response()->json([
            'success' => true,
            'data' => new BackingResource($backing),
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Backing berhasil diupdate',
            'data' => new BackingResource($backing),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Backing $backing): JsonResponse
    {
        $this->backingService->deleteBacking($backing);

        return response()->json([
            'success'=>true,
            'message'=>'Backing berhasil dihapus'
        ]);
    }
}
