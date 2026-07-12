<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Backing;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'backing_id',
        'type',
        'amount',
        'status',
        'reference',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function backing(): BelongsTo
    {
        return $this->belongsTo(Backing::class);
    }
}
