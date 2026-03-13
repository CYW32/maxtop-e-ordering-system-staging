<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity, Searchable;

    protected $table = 'companys'; // Aligning with requested naming

    protected $fillable = [
        'company_code', // HQ Code [1.d]
        'branch_code',  // Branch Code [1.d]
        'parent_id',    // Links Branch to HQ [3.c]
        'catalog_id',   // Whitelist control [1.b]
        'company_name',
        'company_reg_no',
        'pic_name',
        'pic_phone',
        'delivery_address',
        'postal_code',
        'city',
        'state',
    ];

    // Define what the search bar can look for
    protected $searchable = [
        'company_code',
        'company_name',
        'company_reg_no',
        'pic_name',
    ];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function branches()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }
}
