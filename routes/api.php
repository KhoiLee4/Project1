<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GroundController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\VenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::prefix('venues')->group(function () {
    Route::get('/', [VenueController::class, 'index']);
    Route::get('/{id}', [VenueController::class, 'show']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
});

Route::prefix('grounds')->group(function () {
    Route::get('/', [GroundController::class, 'index']);
    Route::get('/{id}', [GroundController::class, 'show']);
});

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::get('/{id}', [EventController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('bookings')->group(function () {
        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show']);
        Route::put('/{id}', [BookingController::class, 'update']);
        Route::delete('/{id}', [BookingController::class, 'destroy']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::put('/{id}', [PaymentController::class, 'update']);
    });

    Route::prefix('ratings')->group(function () {
        Route::post('/', [RatingController::class, 'store']);
        Route::put('/{id}', [RatingController::class, 'update']);
        Route::delete('/{id}', [RatingController::class, 'destroy']);
    });
});

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
});

Route::prefix('ratings')->group(function () {
    Route::get('/', [RatingController::class, 'index']);
    Route::get('/{id}', [RatingController::class, 'show']);
});

Route::prefix('images')->group(function () {
    Route::post('/upload', [\App\Http\Controllers\Api\ImageController::class, 'upload'])->middleware('auth:sanctum');
    Route::get('/{id}', [\App\Http\Controllers\Api\ImageController::class, 'show']);
    Route::delete('/{id}', [\App\Http\Controllers\Api\ImageController::class, 'delete'])->middleware('auth:sanctum');
});
