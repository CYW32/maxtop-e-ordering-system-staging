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
    ];

    protected $searchable = [
        'name',
        'login_id',
        'email',
        'status',
        'parent_id',
        'assigned_cs_id',
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
}
