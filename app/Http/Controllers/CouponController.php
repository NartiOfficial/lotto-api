<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Draw;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

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
            'draw_ids' => 'required|array|min:1|max:10',
            'draw_ids.*' => 'exists:draws,id',
        ], [
            'numbers.required' => 'Musisz wybrać 6 liczb.',
            'numbers.size' => 'Musisz wybrać dokładnie 6 liczb.',
            'numbers.*.integer' => 'Każda liczba musi być liczbą całkowitą.',
            'numbers.*.min' => 'Liczby muszą być większe lub równe 1.',
            'numbers.*.max' => 'Liczby muszą być mniejsze lub równe 49.',
            'draw_ids.required' => 'Musisz przypisać kupon do co najmniej jednego losowania.',
            'draw_ids.min' => 'Musisz przypisać kupon przynajmniej do jednego losowania.',
            'draw_ids.max' => 'Możesz przypisać kupon maksymalnie do 10 losowań.',
            'draw_ids.*.exists' => 'Wybrane losowanie nie istnieje w systemie.',
        ]);

        $invalidDraws = Draw::whereIn('id', $validated['draw_ids'])
            ->where('draw_date', '<', Carbon::now())  
            ->exists();

        if ($invalidDraws) {
            throw ValidationException::withMessages([
                'draw_ids' => ['Nie można kupić kuponu na losowanie, które już się odbyło.'],
            ]);
        }

        $coupon = $request->user()->coupons()->create([
            'numbers' => $validated['numbers'],
        ]);

        $coupon->draws()->attach($validated['draw_ids']);

        return response()->json([
            'message' => 'Kupon został pomyślnie utworzony.',
            'coupon' => $coupon->load('draws'),
        ], 201);
    }

    /**
     * Wyświetlenie wszystkich kuponów użytkownika z paginacją
     */
    public function index(Request $request)
    {
        $coupons = $request->user()->coupons()->with('draws')->paginate(10);

        if ($coupons->isEmpty()) {
            return response()->json([
                'message' => 'Brak kuponów do wyświetlenia.',
            ], 404);
        }

        return response()->json($coupons, 200);
    }

     /**
     * Pobierz dane jednego kuponu.
     */
    public function show($id)
    {
        $coupon = Coupon::with(['user', 'draws'])->find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Kupon nie został znaleziony.'], 404);
        }

        return response()->json($coupon, 200);
    }
}
