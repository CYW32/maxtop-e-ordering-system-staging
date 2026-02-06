<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uom extends Model
{
    use SoftDeletes;

    protected $fillable = ['item_id', 'uom_name', 'rate_qty', 'price'];

    // ARCHITECTURE FIX: Hide price from Customer/Branch roles globally [Addendum 3.b]
    protected function staffPrice(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if (auth()->check() && auth()->user()->hasRole('customer')) {
                    return null;
                }

                return $value;
            }
        );
    }

    public function canBeDeleted(): bool
    {
        // Prevent hard delete if referenced in order history [Addendum 3.c]
        return ! \DB::table('order_items')->where('snapshot_uom_id', $this->id)->exists();
    }
}
