<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Wyświetlenie profilu użytkownika
     */
    public function show(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    /**
     * Aktualizacja profilu użytkownika
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'password' => 'sometimes|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $request->user()->update($validated);

        return response()->json($request->user(), 200);
    }

    /**
     * Historia kuponów użytkownika
     */
    public function couponsHistory(Request $request)
    {
        $coupons = $request->user()->coupons()->with('draws')->get();
        return response()->json($coupons, 200);
    }

    /**
     * Pobranie wszystkich użytkowników.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')->get();
        return response()->json($users, 200);
    }
}
