<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AdminOverviewService
{
    public function overview(): array
    {
        return [
            'campaigns' => [
                'draft' => Campaign::where('status', 'draft')->count(),
                'review' => Campaign::where('status', 'review')->count(),
                'active' => Campaign::where('status', 'active')->count(),
                'success' => Campaign::where('status', 'success')->count(),
                'failed' => Campaign::where('status', 'failed')->count(),
            ],

            'total_collected' => Campaign::sum('collected_amount'),

            'platform_fee' => Transaction::where('type', 'platform_fee')
                ->where('status', 'success')
                ->sum('amount'),
        ];
    }

    public function campaignChart()
    {
        return Campaign::selectRaw("
                MONTH(created_at) as month_number,
                MONTHNAME(created_at) as month,
                COUNT(*) as total_campaign
            ")
            ->groupBy(
                DB::raw("MONTH(created_at)"),
                DB::raw("MONTHNAME(created_at)")
            )
            ->orderBy('month_number')
            ->get();
    }
}