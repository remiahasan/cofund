<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Backing;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class DashboardService
{
    public function creatorDashboard(User $creator): array
    {
        $campaigns = Campaign::withCount('backings')
            ->where('creator_id', $creator->id)
            ->latest()
            ->get();

        $summary = [
            'total_campaign' => $campaigns->count(),

            'active_campaign' => $campaigns
                ->where('status', 'active')
                ->count(),

            'funded_campaign' => $campaigns
                ->where('status', 'funded')
                ->count(),

            'failed_campaign' => $campaigns
                ->where('status', 'failed')
                ->count(),

            'total_backer' => $campaigns->sum('backings_count'),

            'total_collected' => $campaigns->sum('current_amount'),
        ];

        return [
            'summary' => $summary,
            'campaigns' => $campaigns,
        ];
    }

    public function creatorFundingChart(User $creator)
    {
        $dailyFunding = Backing::query()
            ->join('campaigns', 'campaigns.id', '=', 'backings.campaign_id')
            ->where('campaigns.creator_id', $creator->id)
            ->where('backings.status', 'confirmed')
            ->select(
                DB::raw('DATE(backings.created_at) as date'),
                DB::raw('SUM(backings.amount) as total')
            )
            ->groupBy(DB::raw('DATE(backings.created_at)'))
            ->orderBy('date')
            ->get();

        $runningTotal = 0;

        return $dailyFunding->map(function ($item) use (&$runningTotal) {

            $runningTotal += $item->total;

            return [
                'date' => $item->date,
                'amount' => $runningTotal,
            ];

        });
    }

    public function backerSummary(User $user): array
    {
        $backings = Backing::where('user_id', $user->id);

        return [

            'total_backing' => (clone $backings)
                ->where('status', 'confirmed')
                ->sum('amount'),

            'campaign_joined' => (clone $backings)
                ->distinct('campaign_id')
                ->count('campaign_id'),

            'total_refund' => Transaction::where('user_id', $user->id)
                ->where('type', 'refund')
                ->sum('amount'),

        ];
    }
}