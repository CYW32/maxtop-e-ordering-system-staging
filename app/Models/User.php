<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\DateFilterable;
use App\Traits\RoleFilterable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use DateFilterable, HasFactory, HasRoles, Notifiable, RoleFilterable, Searchable;

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
    ];

    protected $searchable = [
        'name',
        'email',
        'login_id',
        'parent_id',
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
}
