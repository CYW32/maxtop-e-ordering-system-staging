<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomerDetail extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_reg_no',
        'pic_name',
        'pic_phone',
        'delivery_address',
        'postal_code',
        'city',
        'state',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Logs company name, reg no, PIC info, etc. [4]
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
