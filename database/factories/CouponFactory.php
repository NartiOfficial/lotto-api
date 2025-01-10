<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = \App\Models\Coupon::class;

    public function definition()
    {
        return [
            'numbers' => $this->faker->unique()->randomElements(range(1, 49), 6),
        ];
    }
}
