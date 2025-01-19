<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    use HasFactory;

    protected $fillable = ['draw_date', 'winning_numbers'];

    protected $casts = [
        'draw_date' => 'datetime',
        'winning_numbers' => 'array',  
    ];

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }    
}
