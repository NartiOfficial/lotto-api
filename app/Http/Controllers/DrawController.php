<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use Illuminate\Http\Request;

class DrawController extends Controller
{
    public function index()
    {
        $draws = Draw::all();
        return response()->json($draws, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'draw_date' => 'required|date',
        ]);

        $draw = Draw::create([
            'draw_date' => $validated['draw_date'],
            'winning_numbers' => null, 
        ]);

        return response()->json($draw, 201);
    }

    public function update(Request $request, $id)
    {
        $draw = Draw::findOrFail($id);

        $validated = $request->validate([
            'winning_numbers' => 'required|array|size:6',
            'winning_numbers.*' => 'integer|min:1|max:49',
        ]);

        $draw->update([
            'winning_numbers' => $validated['winning_numbers'],
        ]);

        return response()->json(['message' => 'Wyniki losowania zostały zaktualizowane.', 'draw' => $draw], 200);
    }

    public function destroy($id)
    {
        $draw = Draw::findOrFail($id);
        $draw->delete();

        return response()->json(['message' => 'Losowanie zostało usunięte.'], 200);
    }
}
