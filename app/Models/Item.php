<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Item extends Model
{
    use LogsActivity;

    protected $fillable = ['sku', 'name', 'description', 'price', 'image_path'];

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
        return ! $this->orderItems()
            ->whereHas('order', function ($q) {
                // Updated to include 'draft' and 'pending' per Section 3C [1]
                $q->whereIn('status', ['draft', 'pending', 'approved', 'in_transit', 'completed']);
            })->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
}
