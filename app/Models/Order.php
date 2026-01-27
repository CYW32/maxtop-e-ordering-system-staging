<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use LogsActivity,Searchable;

    protected $fillable = [
        'order_number',
        'user_id',
        'handler_id',
        'status',
        'cancellation_reason',
        'internal_notes',
    ];

    protected $searchable = [
        'order_number', 'status',
    ];

    /**
     * Relationship: The Customer who owns the order/draft.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: The CS Staff currently handling the order.
     * Fulfills Section 5: Ownership logic [6].
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    /**
     * Relationship: The items contained within this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Order has been {$eventName}");
    }
}
