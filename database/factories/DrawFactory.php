<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DrawFactory extends Factory
{
    protected $model = \App\Models\Draw::class;

    public function definition()
    {
        return [
            'draw_date' => Carbon::now()->addDays($this->faker->numberBetween(1, 30)),
            'numbers' => $this->faker->unique()->randomElements(range(1, 49), 6),
        ];
    }
}
