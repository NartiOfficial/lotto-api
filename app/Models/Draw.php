<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    use HasFactory;

    protected $fillable = [
        'draw_date',
        'numbers',
    ];

    protected $casts = [
        'numbers' => 'array',
        'draw_date' => 'datetime',
    ];

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }
}
