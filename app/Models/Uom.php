<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Uom extends Model
{
    use SoftDeletes;

    protected $fillable = ['item_id', 'uom_name', 'rate_qty', 'price', 'status'];

    /**
     * ARCHITECTURE FIX: Align visibility guard with Addendum 3.b [4]
     */
    protected function staffPrice(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if (auth()->check() && auth()->user()->hasRole('customer')) {
                    return null;
                }

                return $value;
            }
        );
    }

    /**
     * Fulfills Addendum 5.c: Deletion Rule [5].
     * Prevents hard delete if the UOM is referenced in order history.
     *
     * BUG FIX: Column name corrected from 'snapshot_uom_id' to 'uom_id'
     * to match migration 2026_03_15_000001 [2].
     */
    public function canBeDeleted(): bool
    {
        return ! DB::table('order_items')
            ->where('uom_id', $this->id)
            ->exists();
    }

    /**
     * Scope for customer-facing visibility.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * ARCHITECTURE FIX: Snapshot Integrity Link [Backbone 5.c.1].
     * Resolves BadMethodCallException by defining the relationship to historical orders.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
