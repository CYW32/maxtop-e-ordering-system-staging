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
    use LogsActivity, Searchable;

    // Temporary variable to hold the combined text before saving to database
    public $status_change_reason = null; 

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

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
        if ($this->status === 'approved' && auth()->user()->hasRole('cs_staff')) {
            return false;
        }

        return true;
    }

    public function hasPendingCancellationRequest(): bool
    {
        return $this->status === 'cancellation_requested' && ! is_null($this->cancellation_requested_by);
    }

    public function cancellationRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancellation_requested_by');
    }

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

    protected static function booted()
    {
        static::updated(function ($order) {
            
            // 1. Log order status progression
            if ($order->isDirty('status') && auth()->check()) {
                \Illuminate\Support\Facades\DB::table('order_status_history')->insert([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'reason' => $order->status_change_reason, 
                    'changed_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2. Log handler changes (Claims and Handovers)
            if ($order->isDirty('handler_id') && auth()->check()) {
                $oldHandler = $order->getOriginal('handler_id');
                $action = is_null($oldHandler) ? 'claimed' : 'handed_over';

                \Illuminate\Support\Facades\DB::table('order_status_history')->insert([
                    'order_id' => $order->id,
                    'status' => $action,
                    'reason' => $order->status_change_reason, 
                    'changed_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }
}