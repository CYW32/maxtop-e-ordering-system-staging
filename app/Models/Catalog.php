<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Catalog extends Model
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

    public function canBeDeleted(): bool
    {
        // Fulfills Single Catalog Policy: Lock if users are assigned
        // or if the system needs to maintain the record for history.
        return ! (\App\Models\User::where('catalog_id', $this->id)->exists());
    }
}
