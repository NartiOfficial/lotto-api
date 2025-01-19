<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Draw;
use Carbon\Carbon;

class SingleDrawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $drawId = 4;
        $drawDate = Carbon::create(2025, 1, 14, 21, 40, 0);

        Draw::updateOrCreate(
            [
                'id' => $drawId,
            ],
            [
                'draw_date' => $drawDate,
                'winning_numbers' => [19, 28, 44, 45, 47, 48],
            ]
        );

        $this->command->info("Losowanie z ID {$drawId} zostało zaktualizowane lub dodane z wygranymi liczbami: 1, 2, 3, 4, 5, 6.");

        // sail artisan db:seed --class=SingleDrawSeeder
    }
}
