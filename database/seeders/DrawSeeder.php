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
        $startDate = Carbon::now()->subWeeks(2)->startOfDay();
        $endDate = Carbon::now()->addWeeks(5)->endOfDay();
        $lottoTimes = ['21:40'];
        $daysOfWeek = [2, 4, 6];
    
        while ($startDate <= $endDate) {
            foreach ($daysOfWeek as $day) {
                $drawDate = $startDate->copy()->next($day);
                foreach ($lottoTimes as $time) {
                    $drawDateTime = Carbon::parse($drawDate->format('Y-m-d') . " " . $time);
                    if ($drawDateTime <= $endDate) {
                        Draw::firstOrCreate(
                            ['draw_date' => $drawDateTime],
                            // ['winning_numbers' => $drawDateTime < Carbon::now() ? $this->generateRandomNumbers() : null]
                        );
                    }
                }
            }
            $startDate->addWeek();
        }
    
        $this->command->info('Losowania Lotto zostały wygenerowane od 2 tygodni wstecz do 5 tygodni do przodu.');
    }
    
    /**
     * Generuje 6 unikalnych losowych liczb od 1 do 49.
     */
    private function generateRandomNumbers(): array
    {
        $numbers = [];
        while (count($numbers) < 6) {
            $number = rand(1, 49);
            if (!in_array($number, $numbers)) {
                $numbers[] = $number;
            }
        }
        sort($numbers);
        return $numbers;
    }
}