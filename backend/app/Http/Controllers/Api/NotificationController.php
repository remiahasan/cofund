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
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $notifications = $this->notificationService
            ->getNotifications(auth()->user());

        return $this->success(
            'Daftar notifikasi berhasil diambil.',
            NotificationResource::collection($notifications),
            [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        
        return $this->success(
            'Notifikasi berhasil diambil.',
            new NotificationResource($notification->load('user'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification = $this->notificationService
            ->markAsRead($notification);

        return $this->success(
            'Notifikasi berhasil ditandai telah dibaca.',
            new NotificationResource($notification)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $this->notificationService
            ->deleteNotification($notification);

        return $this->success('Notifikasi berhasil dihapus.');
    }

    public function unreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount(auth()->user());

        return $this->success(
            'Jumlah notifikasi belum dibaca berhasil diambil.',
            [
                'unread_count' => $count,
            ]
        );
    }
}

