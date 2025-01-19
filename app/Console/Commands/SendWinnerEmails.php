<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Draw;
use App\Mail\WinnerNotification;
use Illuminate\Support\Facades\Mail;

class SendWinnerEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-winners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wyślij e-maile do zwycięzców loterii';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lastDraw = Draw::whereNotNull('winning_numbers')
            ->orderBy('draw_date', 'desc')
            ->first();
    
        if (!$lastDraw) {
            $this->error('Nie znaleziono żadnego losowania z wynikami.');
            return;
        }
    
        foreach ($lastDraw->users as $user) {
            foreach ($user->coupons as $coupon) {
                if (is_array($lastDraw->winning_numbers) && is_array($coupon->numbers)) {
                    $matchedNumbers = count(array_intersect($lastDraw->winning_numbers, $coupon->numbers));
    
                    if ($matchedNumbers >= 3) {
                        $this->sendWinningEmail($user->email, $matchedNumbers);
                    }
                }
            }
        }
    
        $this->info('E-maile zostały wysłane dla ostatniego losowania.');
    }
    

    private function sendWinningEmail(string $email, int $matchedNumbers)
    {
        $prizes = [
            3 => "100",
            4 => "1000",
            5 => "10000",
            6 => "1000000",
        ];
        $prize = $prizes[$matchedNumbers] ?? "0";

        Mail::to($email)->send(new WinnerNotification($matchedNumbers, $prize));
    }
}
