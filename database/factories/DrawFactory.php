<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DrawFactory extends Factory
{
    public function definition()
    {
        return [
            'draw_date' => Carbon::now()->addDays($this->faker->numberBetween(1, 30)),
            'winning_numbers' => null,
        ];
    }
}
