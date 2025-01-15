<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Draw;
use Carbon\Carbon;

class GenerateDraws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'draws:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure there are always 6 upcoming draws and update null draws';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateNullDraws();

        $futureDrawsCount = Draw::where('draw_date', '>', Carbon::now())->count();

        $neededDraws = 6 - $futureDrawsCount;

        if ($neededDraws > 0) {
            $this->generateFutureDraws($neededDraws);
        }

        $this->info('Losowania zostały zaktualizowane i uzupełnione.');
        return 0;
    }

    /**
     * Aktualizacja losowań bez wyników.
     */
    protected function updateNullDraws()
    {
        $drawsToUpdate = Draw::whereNull('winning_numbers')->where('draw_date', '<=', Carbon::now())->get();

        foreach ($drawsToUpdate as $draw) {
            $winningNumbers = collect(range(1, 49))->random(6)->all();
            $draw->update([
                'winning_numbers' => $winningNumbers,
            ]);
            $this->info('Zaktualizowano wyniki losowania z datą: ' . $draw->draw_date->toDateTimeString());
        }
    }

    /**
     * Generowanie przyszłych losowań do przodu.
     */
    protected function generateFutureDraws($count)
    {
        $date = Carbon::now();

        while ($count > 0) {
            $date->addDay();

            if (in_array($date->dayOfWeek, [2, 4, 6])) {
                Draw::firstOrCreate([
                    'draw_date' => $date->setTime(21, 40, 0),
                ]);
                $this->info('Utworzono losowanie na: ' . $date->toDateTimeString());
                $count--;
            }
        }
    }
}
