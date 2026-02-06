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
        'cancellation_requested_by',
        'cancellation_request_reason',
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

    public function canBeCancelledDirectly(): bool
    {
        // Approved orders require a request if the user is CS Staff [Addendum 2.a]
        if ($this->status === 'approved' && auth()->user()->hasRole('cs_staff')) {
            return false;
        }

        return true;
    }

    /**
     * Check if there is an active cancellation request pending review.
     */
    public function hasPendingCancellationRequest(): bool
    {
        return ! is_null($this->cancellation_requested_by) && $this->status === 'approved';
    }

    /**
     * Get the user who requested the cancellation.
     */
    public function cancellationRequester(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'cancellation_requested_by');
    }

    /**
     * Fulfills Section 5: Assignment & Handover Logic
     * Scopes the query to orders relevant to a specific staff member.
     */
    public function scopeAssignedTo($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('handler_id', $user->id)
                ->orWhere(function ($sub) use ($user) {
                    $sub->whereNull('handler_id')
                        ->whereHas('user', function ($u) use ($user) {
                            $u->where('assigned_cs_id', $user->id);
                        });
                });
        });
    }
}
