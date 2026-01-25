<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use DateFilterable, HasFactory, HasRoles, LogsActivity, Notifiable, RoleFilterable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'login_id',
        'email',
        'password',
        'status',
        'parent_id',
        'assigned_cs_id',
        'catalog_id',
    ];

    protected $searchable = [
        'name',
        'login_id',
        'email',
        'status',
        'parent_id',
        'assigned_cs_id',
        'catalog_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 新增：获取所属总店
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // 新增：获取下属分店
    public function branches()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function details()
    {
        // A user has one set of customer details
        return $this->hasOne(CustomerDetail::class);
    }

    // For CS Staff: Get all customers they handle
    public function assignedCustomers()
    {
        return $this->hasMany(User::class, 'assigned_cs_id');
    }

    // For Customers: Get their assigned CS Staff
    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_cs_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Automatically logs all fields in $fillable [3]
            ->logOnlyDirty() // Only log fields that actually changed
            ->dontSubmitEmptyLogs();
    }

    /**
     * Fulfills Single Catalog Policy
     * Standard Eloquent relationship for the assigned catalog.
     */
    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id');
    }

    /**
     * Inheritance Logic: Get the effective catalog ID
     * If the branch has no catalog, it looks up the parent (Main) catalog.
     */
    public function getEffectiveCatalogId()
    {
        // 1. Direct Assignment
        if ($this->catalog_id) {
            return $this->catalog_id;
        }

        // 2. Inheritance: If Branch, check Main Store
        if ($this->parent_id) {
            return $this->parent?->catalog_id;
        }

        return null;
    }

    /**
     * Whitelist Logic: Restricted Item Visibility
     * Ensures customers only see items in their specific assigned (or inherited) catalog.
     */
    public function getVisibleItems()
    {
        $catalogId = $this->getEffectiveCatalogId();

        if (! $catalogId) {
            return collect(); // Return empty if no catalog is assigned to the chain
        }

        return Item::whereHas('catalogs', function ($q) use ($catalogId) {
            $q->where('catalogs.id', $catalogId);
        })->get();
    }

    /**
     * Fulfills Request: Check if the user has an order currently awaiting review.
     */
    public function hasPendingOrder(): bool
    {
        return $this->orders()->where('status', 'pending')->exists();
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * Fulfills Section 4.1: Single Draft Policy
     * Retrieves the current active draft or returns null.
     */
    public function currentDraft()
    {
        return $this->orders()->where('status', 'draft')->first();
    }

    /**
     * Refined Section 4.a.1: Logic to prevent draft creation if a pending order exists.
     */
    public function getOrCreateDraft()
    {
        $draft = $this->currentDraft();

        if (! $draft) {
            // Architecture Check: Block creation if a pending order is already in the queue
            if ($this->hasPendingOrder()) {
                return null;
            }
            $draft = $this->orders()->create(['status' => 'draft']);
        }

        return $draft;
    }

    /**
     * Refined Section 3.c.1: Deletion Restriction Logic
     * A user can be deleted if they have no orders.
     * If they are an HQ, all their branches must also have no orders.
     */
    public function canBeDeleted(): bool
    {
        // 1. Check if the specific user has orders [4]
        if ($this->orders()->exists()) {
            return false;
        }

        // 2. Fulfills Request: If Main HQ, check if any branch has orders
        if (is_null($this->parent_id)) {
            $hasBranchWithOrders = $this->branches()->whereHas('orders')->exists();
            if ($hasBranchWithOrders) {
                return false;
            }
        }

        return true;
    }
}
