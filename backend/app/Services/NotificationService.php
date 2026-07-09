<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function getNotifications(User $user): LengthAwarePaginator
    {
        return $user->notifications()
            ->latest()
            ->paginate(10);
    }

    public function createNotification(
        User $user,
        string $type,
        string $title,
        string $body,
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }

    public function sendCampaignSuccess(Campaign $campaign): void
    {
        $this->create(
            $campaign->creator,
            'campaign_success',
            'Campaign berhasil',
            'Dana campaign telah dicairkan.',
            [
                'campaign_id' => $campaign->id,
            ]
        );
    }

    public function sendCampaignFailed(Campaign $campaign): void
    {
        foreach ($campaign->backings as $backing) {
            $this->create(
                $backing->user,
                'campaign_failed',
                'Campaign gagal',
                'Dana backing telah dikembalikan.',
                [
                    'campaign_id' => $campaign->id,
                ]
            );
        }
    }

    public function sendDeadlineNotification(Campaign $campaign): void
    {
        $days = now()->diffInDays($campaign->deadline);
        foreach ($campaign->backings as $backing) {
            $this->create(
                $backing->user,
                'deadline',
                "Deadline H-{$days}",
                "Campaign {$campaign->title} akan berakhir {$days} hari lagi.",
                [
                    'campaign_id' => $campaign->id,
                ]
            );
        }
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->update([
            'read_at' => now(),
        ]);

        return $notification->fresh();
    }

    public function deleteNotification(Notification $notification): bool
    {
        return $notification->delete();
    }
}