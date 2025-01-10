<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ManageCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage coupons';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Managing coupons...');
        return 0;
    }
}
