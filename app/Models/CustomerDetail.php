<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
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
}
