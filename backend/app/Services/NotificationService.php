<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Campaign;
use App\Models\Backing;
use App\Models\CampaignUpdate;

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

    public function sendCampaignApproved(Campaign $campaign): void
    {
        $this->createNotification(
            $campaign->creator,
            'campaign_success',
            'Campaign disetujui',
            'Campaign anda telah disetujui dan dapat dilihat oleh publik.',
            [
                'campaign_id' => $campaign->id,
            ]
        );
    }

    public function sendCampaignRejected(Campaign $campaign): void
    {
        $this->createNotification(
            $campaign->creator,
            'campaign_failed',
            'Campaign ditolak',
            'Campaign anda tidak disetujui dan tidak dapat dilihat oleh publik.',
            [
                'campaign_id' => $campaign->id,
            ]
        );
    }

    public function sendNewBacker(Backing $backing): void
    {
        $this->createNotification(
            $backing->campaign->creator,
            'new_backing',
            'Backing baru',
            "{$backing->campaign->name} telah melakukan backing kepada campaign anda.",
            [
                'campaign_id' => $backing->campaign_id,
                'backing_id' => $backing->id,
            ]
        );
    }

    public function backingConfirmed(Backing $backing): void
    {
        $this->createNotification(
            $backing->user,
            'backing_success',
            'Backing berhasil',
            "Backing anda telah dikonfirmasi.",
            [
                'campaign_id' => $backing->campaign_id,
                'backing_id' => $backing->id,
            ]
        );
    }

    public function sendCampaignDisbursed(Campaign $campaign): void
    {
        $this->createNotification(
            $campaign->creator,
            'campaign_disbursed',
            'Dana berhasil dicairkan',
            'Campaign anda berhasil mencapai target dan dana telah dicairkan.',
            [
                'campaign_id' => $campaign->id,
            ]
        );
    }

    public function sendCampaignUpdate(CampaignUpdate $update): void
    {
        $campaign = $update->campaign;
        foreach ($campaign->backings as $backing) {
            $this->createNotification(
                $backing->user,
                'campaign_update',
                'Update campaign',
                $update->title,
                [
                    'campaign_id' => $update->campaign_id,
                    'update_id' => $update->id,
                ]
            );
        }
    }

    public function sendCampaignRefunded(Campaign $campaign): void
    {
        foreach ($campaign->backings as $backing) {
            $this->createNotification(
                $backing->user,
                'campaign_refunded',
                'Dana campaign berhasil dikembalikan',
                'Dana campaign anda telah dikembalikan karena campaign gagal mencapai target.',
                [
                    'campaign_id' => $campaign->id,
                    'backing_id' => $backing->id,
                ]
            );
        }
    }

    public function sendCampaignDeadlineReminder(Campaign $campaign, int $days): void
    {
        foreach ($campaign->backings as $backing) {
            $this->createNotification(
                $backing->user,
                'deadline',
                "Deadline H-{$days}",
                "Campaign {$campaign->title} akan berakhir {$days} hari lagi.",
                [
                    'campaign_id' => $campaign->id,
                    'day'=> $days,
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

    public function getUnreadCount(User $user): int
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->count();
    }
}