<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = ['numbers', 'user_id'];

    protected $casts = [
        'numbers' => 'array',
    ];

    public function draws()
    {
        return $this->belongsToMany(Draw::class, 'coupon_draw', 'coupon_id', 'draw_id')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
