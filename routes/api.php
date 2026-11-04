<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\TernakApiController;
use Illuminate\Support\Facades\Route;

// Semua URL di file ini otomatis berawalan /api  (contoh: POST /api/login)

// Publik: login untuk mendapat token (dibatasi 5 percobaan per menit)
Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:5,1');

// Wajib membawa token Bearer
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // Melihat data ternak: pemilik, pekerja, dokter hewan
    Route::middleware('role:pemilik,pekerja,dokter_hewan')->group(function () {
        Route::get('/ternak', [TernakApiController::class, 'index']);
        Route::get('/ternak/{ternak}', [TernakApiController::class, 'show']);
    });

    // Mengubah data ternak: pemilik dan pekerja
    Route::middleware('role:pemilik,pekerja')->group(function () {
        Route::post('/ternak', [TernakApiController::class, 'store']);
        Route::put('/ternak/{ternak}', [TernakApiController::class, 'update']);
        Route::delete('/ternak/{ternak}', [TernakApiController::class, 'destroy']);
    });
});
