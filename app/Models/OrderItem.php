<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_id',
        'uom_id', // ARCHITECTURE FIX: Ensure UOM ID is fillable for grouping
        'snapshot_name',
        'snapshot_uom_name', // Fulfills Audit Snapshot requirement [10.b]
        'snapshot_uom_rate',
        'quantity',
        'price_at_order',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * ARCHITECTURE FIX: Defined missing relationship to resolve "UNIT" display bug.
     */
    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class)->withTrashed(); // Include trashed for historical consistency
    }
}
