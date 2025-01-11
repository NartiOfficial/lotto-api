<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Draw;
use Carbon\Carbon;

class FillPastDrawsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $currentDate = Carbon::now();

        $drawsToUpdate = Draw::whereNull('winning_numbers')
            ->where('draw_date', '<=', $currentDate)
            ->orderBy('draw_date', 'asc')
            ->get();

        if ($drawsToUpdate->isEmpty()) {
            $this->command->info('Brak losowań do uzupełnienia.');
            return;
        }

        foreach ($drawsToUpdate as $draw) {
            $winningNumbers = collect(range(1, 49))->random(6)->all();
            $draw->update([
                'winning_numbers' => $winningNumbers,
            ]);

            $this->command->info('Uzupełniono wyniki losowania z datą: ' . $draw->draw_date->toDateTimeString());
        }

        $this->command->info('Wszystkie brakujące losowania zostały uzupełnione!');
    }
}
