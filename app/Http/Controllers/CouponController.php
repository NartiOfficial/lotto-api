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
     * Pobranie kuponu do edytowania
    */
    public function show($id)
    {
        try {
            $coupon = Coupon::with(['user', 'draws'])->findOrFail($id);
            return response()->json($coupon);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Kupon nie znaleziony'], 404);
        }
    }

    /** 
     * Aktualizacja kuponu
    */
     public function update(Request $request, $id)
     {
         $validator = Validator::make($request->all(), [
             'numbers' => 'required|array|size:6',
             'numbers.*' => 'integer|between:1,49',
             'user_id' => 'required|exists:users,id',
             'draw_ids' => 'required|array|min:1',
             'draw_ids.*' => 'exists:draws,id'
         ]);
 
         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 400);
         }
 
         try {
             $coupon = Coupon::findOrFail($id);
 
             $coupon->numbers = $request->numbers;
             $coupon->user_id = $request->user_id;
             $coupon->save();
 
             $coupon->draws()->sync($request->draw_ids);
 
             return response()->json(['message' => 'Kupon zaktualizowany pomyślnie!', 'coupon' => $coupon]);
         } catch (\Exception $e) {
             return response()->json(['message' => 'Błąd podczas aktualizacji kuponu'], 500);
         }
     }

    public function getAllCoupons(Request $request)
    {
        $coupons = Coupon::with(['user', 'draws'])->paginate(10);

        if ($coupons->isEmpty()) {
            return response()->json([
                'message' => 'Brak kuponów do wyświetlenia.',
            ], 404);
        }

        return response()->json($coupons, 200);
    }

    public function checkResultsByTicketId(Request $request, $ticketId)
    {
        $user = $request->user();
    
        \Log::info('Zalogowany użytkownik:', ['user_id' => $user->id]);
    
        $coupon = Coupon::where('user_id', $user->id)
            ->with('draws')
            ->where('id', $ticketId)
            ->first();
    
        if (!$coupon) {
            \Log::error('Kupon nie został znaleziony', [
                'user_id' => $user->id,
                'ticket_id' => $ticketId
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
            ], 404);
        }
    
        $results = [];
    
        foreach ($coupon->draws as $draw) {
            $matchedNumbers = $draw->winning_numbers
                ? count(array_intersect($coupon->numbers, $draw->winning_numbers))
                : null;
    
            $results[] = [
                'draw_id' => $draw->id,
                'draw_date' => $draw->draw_date,
                'user_numbers' => $coupon->numbers,
                'winning_numbers' => $draw->winning_numbers,
                'matched_numbers' => $matchedNumbers,
                'status' => $draw->winning_numbers ? 'completed' : 'pending',
            ];
        }
    
        return response()->json([
            'success' => true,
            'ticket_id' => $coupon->id,
            'results' => $results,
        ]);
    }
    
}
