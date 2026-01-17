<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_id',
        'snapshot_name',
        'quantity',
        'price_at_order',
    ];

    /**
     * Relationship: The parent order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: The original product item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
