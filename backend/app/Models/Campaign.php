<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CampaignImage;
use App\Models\CampaignUpdate;
use App\Models\CampaignTier;
use App\Models\Backing;
use App\Models\User;
use App\Models\Category;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'target_amount',
        'collected_amount',
        'deadline',
        'video_url',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'deadline' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images():HasMany
    {
        return $this->hasMany(CampaignImage::class);
    }

    public function updates():HasMany
    {
        return $this->hasMany(CampaignUpdate::class);
    }

    public function tiers():HasMany
    {
        return $this->hasMany(CampaignTier::class);
    }

    public function backings():HasMany
    {
        return $this->hasMany(Backing::class);
    }

}
