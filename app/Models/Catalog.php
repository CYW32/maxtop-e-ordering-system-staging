<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Catalog extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $fillable = ['name', 'status'];

    protected $searchable = ['name', 'status'];

    public function items()
    {
        return $this->belongsToMany(Item::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    /**
     * Fulfills Backbone 3.a.1 & Addendum 1.b:
     * Prevents deletion if any Company (HQ or Branch) is assigned to this catalog.
     */
    public function canBeDeleted(): bool
    {
        // ARCHITECTURE FIX: Check assignments in 'companys' table, not 'users' [Addendum 1.b]
        return ! (\App\Models\Company::where('catalog_id', $this->id)->exists());
    }
}
