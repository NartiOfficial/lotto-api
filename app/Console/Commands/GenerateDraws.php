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
    protected $description = 'Generate scheduled lottery draws';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $startDate = Carbon::now()->addDay();
        $endDate = $startDate->copy()->addDays($count);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            Draw::firstOrCreate([
                'draw_date' => $date->setTime(20, 0, 0), // Losowanie codziennie o 20:00
            ]);
        }

        $this->info('Losowania zostały wygenerowane.');

        return 0;
    }
}
