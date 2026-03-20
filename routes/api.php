<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ReservationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rooms
Route::get('/rooms', [RoomController::class, 'index']);

// Reservations (CRUD completo)
Route::apiResource('reservations', ReservationController::class);