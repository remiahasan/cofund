<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Backing;
use App\Models\Campaign;

class CampaignTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'minimum_amount',
        'quota',
        'remaining_quota',
        'reward_description',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function backings()
    {
        return $this->hasMany(Backing::class);
    }

    public function isUnlimited(): bool
    {
        return $this->quota === 0;
    }

    public function hasRemainingQuota(): bool
    {
    return $this->isUnlimited() || $this->remaining_quota > 0;
}
}
