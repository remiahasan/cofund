<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'minimum_amount',
        'quota',
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
}
