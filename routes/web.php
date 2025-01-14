<?php

use Illuminate\Support\Facades\Route;

// Trasa zdrowia
Route::get('/up', function () {
    return response()->json(['status' => 'up'], 200);
});

// Tymczasowa trasa testowa
Route::get('/test-route', function () {
    return response()->json(['message' => 'Test route works'], 200);
});

