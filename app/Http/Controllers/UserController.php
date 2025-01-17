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
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:user,admin',
            'password' => 'sometimes|min:8|confirmed',
        ]);
    
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
    
        $user = User::findOrFail($id);
        $user->update($validated);
    
        if (isset($validated['role'])) {
            $user->syncRoles($validated['role']);
        }
    
        return response()->json($user, 200);
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

    /**
     * Usunięcie użytkownika
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Użytkownik został pomyślnie usunięty.'], 200);
    }
}
