<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DrawController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TestDataController;

// Trasy otwarte (niezabezpieczone)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Trasy zabezpieczone (wymagają autoryzacji)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Zarządzanie użytkownikami
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::get('/user/coupons', [UserController::class, 'couponsHistory']);

    // Zarządzanie kuponami
    Route::post('/coupons', [CouponController::class, 'store']);
    Route::get('/coupons', [CouponController::class, 'index']);
    Route::get('/coupons/results', [CouponController::class, 'checkResults']);

    // Zarządzanie losowaniami
    Route::get('/draws', [DrawController::class, 'index']);
    Route::get('/draws/{id}', [DrawController::class, 'show']);
});

// Trasy dla administratorów
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::put('/admin/draws/{id}', [AdminController::class, 'updateDrawResult']);
    Route::delete('/admin/coupons/{id}', [AdminController::class, 'deleteCoupon']);
    Route::put('/admin/users/{id}/block', [AdminController::class, 'blockUser']);
    Route::post('/test-data/seed', [TestDataController::class, 'seed']);
});
