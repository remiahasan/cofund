<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $notifications = $this->notificationService
            ->getNotifications(auth()->user());

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        return response()->json([
            'success' => true,
            'data' => new NotificationResource(
                $notification->load('user')
            ),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification = $this->notificationService
            ->markAsRead($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai telah dibaca.',
            'data' => new NotificationResource($notification),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $this->notificationService
            ->deleteNotification($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus.',
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => auth()->user()
                    ->notifications()
                    ->whereNull('read_at')
                    ->count(),
            ],
        ]);
    }
}
