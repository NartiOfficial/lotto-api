<?php

use Illuminate\Support\Facades\Route;

// Trasa główna
Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the Lotto API'], 200);
});

// Trasa zdrowia
Route::get('/up', function () {
    return response()->json(['status' => 'up'], 200);
});

// Tymczasowa trasa testowa
Route::get('/test-route', function () {
    return response()->json(['message' => 'Test route works'], 200);
});
