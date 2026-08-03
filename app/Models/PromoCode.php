<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $table = 'promo_codes';

    public $timestamps = false;

    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value', 'max_discount',
        'usage_limit', 'usage_count', 'start_date', 'end_date', 'is_active',
        'created_by', 'created_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
