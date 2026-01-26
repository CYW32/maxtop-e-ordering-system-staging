<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'status'];

    public function items()
    {
        return $this->belongsToMany(Item::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    /**
     * Fulfills Section 3.c Deletion Restriction:
     * A category can only be hard deleted if none of the items
     * currently assigned to it have transaction history.
     */
    public function canBeDeleted(): bool
    {
        // Check if any items in this category exist in the order_items table
        return ! $this->items()->whereHas('orderItems')->exists();
    }
}
