<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController; // Import Controller Lapangan
use App\Http\Controllers\BookingController; // Import Controller Booking

// --- Public Routes (Bisa diakses tanpa login) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/fields', [FieldController::class, 'index']); // Melihat daftar lapangan

// --- Protected Routes (Wajib pakai Token JWT) ---
Route::middleware('auth:api')->group(function () {
    
    // --- KHUSUS ADMIN ---
    // Bungkus rute ini dengan middleware 'admin' agar customer tertolak
    Route::middleware('admin')->group(function () {
        Route::post('/fields', [FieldController::class, 'store']);
        Route::put('/fields/{id}', [FieldController::class, 'update']);
        Route::delete('/fields/{id}', [FieldController::class, 'destroy']);
        Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
    });

    // --- UMUM (Admin & Customer) ---
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::post('/logout', [AuthController::class, 'logout']);
});