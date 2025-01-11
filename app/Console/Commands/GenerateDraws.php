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
    protected $signature = 'draws:generate {--count=7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update draws with results and generate future draws';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $startDate = Carbon::now();
        $endDate = $startDate->copy()->addDays($count);

        $this->updateNullDraws();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if (in_array($date->dayOfWeek, [2, 4, 6])) { 
                Draw::firstOrCreate([
                    'draw_date' => $date->setTime(21, 40, 0), 
                ]);
            }
        }

        $this->info('Losowania zostały zaktualizowane i utworzono przyszłe losowania.');

        return 0;
    }

    /**
     * Uaktualnienie losowań, które mają null w polu `winning_numbers`.
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
}
