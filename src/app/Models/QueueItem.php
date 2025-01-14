<?php

namespace App\Models;

use App\Domain\Enum\WebhookStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'retry_at' => 'datetime',
        'status' => WebhookStatus::class
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
