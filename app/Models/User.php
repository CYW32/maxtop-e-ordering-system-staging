<?php

namespace App\Models;

use App\Traits\DateFilterable;
use App\Traits\RoleFilterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ARCHITECTURE FIX: Required for factories
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    // ARCHITECTURE FIX: Added HasFactory trait to resolve the "Call to undefined method" error
    use DateFilterable, HasFactory, HasRoles, LogsActivity, Notifiable, RoleFilterable, Searchable;

    protected $fillable = ['name', 'login_id', 'email', 'password', 'status', 'company_id', 'assigned_cs_id'];

    protected $searchable = ['name', 'login_id', 'email', 'status'];

    /**
     * Fulfills Spatie LogsActivity requirement.
     * Captures changes to user credentials and company assignments [6].
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Many Users -> One Company [7, 8]
     */
    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Whitelist Logic: Traverses Company -> Parent Company [9]
     */
    public function getEffectiveCatalogId()
    {
        if (! $this->company) {
            return null;
        }

        // Return direct company catalog or inherit from parent HQ [9]
        return $this->company->catalog_id ?? $this->company->parent?->catalog_id;
    }

    /**
     * Retrieve items based on the Single Catalog Policy whitelist [9].
     */
    public function getVisibleItems(): \Illuminate\Support\Collection
    {
        $catalogId = $this->getEffectiveCatalogId();

        return $catalogId
            ? Item::whereHas('catalogs', fn ($q) => $q->where('catalogs.id', $catalogId))->get()
            : collect();
    }

    /**
     * Fulfills Section 3.c Deletion Restriction.
     * A user login can only be deleted if they have no order history.
     */
    public function canBeDeleted(): bool
    {
        // Check for existence in the orders table
        return ! $this->hasMany(Order::class)->exists();
    }
}
