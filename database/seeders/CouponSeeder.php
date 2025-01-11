<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Draw;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::role('user')->get();  
        $draws = Draw::take(10)->get(); 

        foreach ($users as $user) {
            for ($i = 1; $i <= 3; $i++) {
                $coupon = $user->coupons()->create([
                    'numbers' => collect(range(1, 49))->random(6)->all(),
                ]);
                $coupon->draws()->attach($draws->pluck('id')->random(3));
            }
        }

        $this->command->info('Kupony użytkowników zostały utworzone!');
    }
}
