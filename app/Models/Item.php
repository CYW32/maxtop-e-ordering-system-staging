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

    // Logic for Deletion Restriction [2]
    public function canBeDeleted(): bool
    {
        // Check if item exists in any order_items (to be built next)
        // return !$this->orderItems()->exists();

        // TEMPORARY: Return true until Order logic is added
        return true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
}
