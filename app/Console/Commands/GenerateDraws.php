<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Draw;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Generuje losowania';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dump('--- Start generowania losowań ---');

        $lastDrawDate = Draw::max('draw_date') ? Carbon::parse(Draw::max('draw_date')) : Carbon::now()->subDays(10);
        $currentDate = Carbon::now();

        $this->info("Ostatnia znana data losowania: {$lastDrawDate->toDateTimeString()}");
        $this->info("Aktualna data: {$currentDate->toDateTimeString()}");

        $this->updateNullDraws();

        $this->generateFutureDraws($lastDrawDate, $currentDate);

        dump('--- Koniec generowania losowań ---');
        $this->info('Wszystkie losowania zostały zaktualizowane.');
    }

    /**
     * Aktualizacja losowań bez wyników (winning_numbers == null).
     */
    protected function updateNullDraws()
    {
        $drawsToUpdate = Draw::whereNull('winning_numbers')
            ->where('draw_date', '<=', Carbon::now())
            ->get();

        foreach ($drawsToUpdate as $draw) {
            $winningNumbers = collect(range(1, 49))->random(6)->all();
            $draw->update(['winning_numbers' => $winningNumbers]);
            $this->info('Zaktualizowano losowanie z datą: ' . $draw->draw_date->toDateTimeString());
        }
    }

    /**
     * Generowanie brakujących przyszłych losowań.
     */
    protected function generateFutureDraws($lastDrawDate, $currentDate)
    {
        $futureDrawsNeeded = 6 - Draw::where('draw_date', '>', $currentDate)->count();

        while ($futureDrawsNeeded > 0) {
            $lastDrawDate->addDay();

            if (in_array($lastDrawDate->dayOfWeek, [2, 4, 6])) {
                Draw::firstOrCreate([
                    'draw_date' => $lastDrawDate->setTime(21, 40, 0),
                ]);
                $this->info('Utworzono przyszłe losowanie na: ' . $lastDrawDate->toDateTimeString());
                $futureDrawsNeeded--;
            }
        }
    }
}
