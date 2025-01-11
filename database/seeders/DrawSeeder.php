<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Draw;
use Carbon\Carbon;

class DrawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $currentDate = Carbon::now()->subDays(10)->startOfDay();
        $lottoTimes = ['21:40'];
        $daysOfWeek = [2, 4, 6];

        for ($i = 0; $i < 5; $i++) {
            foreach ($daysOfWeek as $day) {
                $drawDate = $currentDate->next($day);
                foreach ($lottoTimes as $time) {
                    Draw::factory()->create([
                        'draw_date' => Carbon::parse($drawDate->format('Y-m-d') . " " . $time),
                        'winning_numbers' => null
                    ]);
                }
            }
            $currentDate->addWeek();
        }

        $this->command->info('Losowania Lotto na najbliższe 5 tygodni zostały utworzone!');
    }
}
