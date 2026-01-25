<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Item extends Model
{
    use LogsActivity;

    protected $fillable = ['sku', 'name', 'description', 'price', 'image_path', 'status'];

    public function catalogs()
    {
        return $this->belongsToMany(Catalog::class);
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    /**
     * Fulfills Section 3C: Deletion & Draft Protection
     * Items with ANY transaction record (including Drafts and Pending) cannot be deleted. [1]
     */
    public function canBeDeleted(): bool
    {
        // Fulfills Section 3.c.1 & 3.c.2: Lock if in ANY order status
        return ! $this->orderItems()->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // Inside Item class
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Fulfills Section 3.c.2: Check if item is in any active draft.
     */
    public function isInDraft(): bool
    {
        return $this->orderItems()->whereHas('order', function ($query) {
            $query->where('status', 'draft');
        })->exists();
    }
}
