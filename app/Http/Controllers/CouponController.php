<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Tworzenie nowego kuponu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numbers' => 'required|array|size:6',
            'numbers.*' => 'integer|min:1|max:49',
            'draw_ids' => 'required|array',
            'draw_ids.*' => 'exists:draws,id',
        ], [
            'numbers.required' => 'Musisz wybrać 6 liczb.',
            'numbers.size' => 'Musisz wybrać dokładnie 6 liczb.',
            'numbers.*.integer' => 'Liczby muszą być liczbami całkowitymi.',
            'numbers.*.min' => 'Liczby muszą być większe lub równe 1.',
            'numbers.*.max' => 'Liczby muszą być mniejsze lub równe 49.',
            'draw_ids.required' => 'Musisz przypisać kupon do co najmniej jednego losowania.',
            'draw_ids.*.exists' => 'Jedno z wybranych losowań nie istnieje.',
        ]);

        $coupon = $request->user()->coupons()->create([
            'numbers' => $validated['numbers'],
        ]);

        $coupon->draws()->attach($validated['draw_ids']);

        return response()->json($coupon->load('draws'), 201);
    }

    /**
     * Wyświetlenie wszystkich kuponów użytkownika z paginacją
     */
    public function index(Request $request)
    {
        $coupons = $request->user()->coupons()->with('draws')->paginate(10);
        return response()->json($coupons, 200);
    }
}
