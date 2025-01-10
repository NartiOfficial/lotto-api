<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use Illuminate\Http\Request;

class DrawController extends Controller
{
    /**
     * Wyświetlenie listy losowań z paginacją, filtrowaniem i sortowaniem
     */
    public function index(Request $request)
    {
        $query = Draw::query();

        if ($request->has('from')) {
            $query->where('draw_date', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('draw_date', '<=', $request->to);
        }

        $draws = $query->orderBy('draw_date', 'desc')->paginate(10);

        return response()->json($draws, 200);
    }

    /**
     * Wyświetlenie szczegółów losowania
     */
    public function show($id)
    {
        $draw = Draw::with('coupons')->findOrFail($id);
        return response()->json($draw, 200);
    }
}
