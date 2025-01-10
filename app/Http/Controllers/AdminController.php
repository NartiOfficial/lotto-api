<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Draw;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Aktualizacja wyników losowania
     */
    public function updateDrawResult(Request $request, $id)
    {
        $request->validate([
            'numbers' => 'required|array|size:6',
            'numbers.*' => 'integer|min:1|max:49',
        ]);

        $draw = Draw::findOrFail($id);
        $draw->numbers = $request->numbers;
        $draw->save();

        return response()->json($draw, 200);
    }

    /**
     * Usuwanie kuponu
     */
    public function deleteCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json(['message' => 'Kupon usunięty'], 200);
    }

    /**
     * Blokowanie użytkownika
     */
    public function blockUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_blocked' => true]);

        return response()->json(['message' => 'Użytkownik zablokowany'], 200);
    }
}
