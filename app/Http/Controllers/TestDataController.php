<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Draw;
use Illuminate\Support\Facades\Hash;

class TestDataController extends Controller
{
    /**
     * Inicjalizacja danych testowych
     */
    public function seed()
    {
        User::factory()->count(10)->create();

        Draw::factory()->count(5)->create();

        $users = User::all();
        $draws = Draw::all();

        foreach ($users as $user) {
            Coupon::factory()->count(3)->create([
                'user_id' => $user->id,
            ])->each(function ($coupon) use ($draws) {
                $coupon->draws()->attach($draws->random(2)->pluck('id')->toArray());
            });
        }

        return response()->json(['message' => 'Dane testowe zostały zaimportowane'], 201);
    }
}
