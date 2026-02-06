<?php

namespace App\Models;

use App\Traits\DateFilterable;
use App\Traits\RoleFilterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use DateFilterable, HasFactory, HasRoles, LogsActivity, Notifiable, RoleFilterable, Searchable;

    protected $fillable = ['name', 'login_id', 'email', 'password', 'status', 'company_id', 'assigned_cs_id'];

    protected $searchable = ['name', 'login_id', 'email', 'status'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    /**
     * Many Users -> One Company
     */
    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relationship: Historical and active orders/drafts.
     */
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Fulfills Backbone 4.b: Pending Review check.
     * Prevents adding new items to a draft if an order is currently awaiting CS review.
     */
    public function hasPendingOrder(): bool
    {
        return $this->orders()->where('status', 'pending')->exists();
    }

    /**
     * Fulfills Backbone 4.a: Single Draft Policy.
     * Retrieves the current active draft for this user.
     */
    public function currentDraft(): ?Order
    {
        return $this->orders()->where('status', 'draft')->first();
    }

    /**
     * Fulfills ReservationController@store requirement.
     * Returns the existing draft or initializes a new one.
     */
    public function getOrCreateDraft(): Order
    {
        return $this->orders()->firstOrCreate(
            ['status' => 'draft'],
            ['order_number' => null]
        );
    }

    /**
     * Whitelist Logic: Traverses Company -> Parent Company
     */
    public function getEffectiveCatalogId()
    {
        if (! $this->company) {
            return null;
        }

        return $this->company->catalog_id ?? $this->company->parent?->catalog_id;
    }

    /**
     * Retrieve items based on the Single Catalog Policy whitelist.
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
     */
    public function canBeDeleted(): bool
    {
        return ! $this->orders()->exists();
    }
}
