<?php

use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\ContributionController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/boards', [BoardController::class, 'index']);
    Route::get('/boards/{board}', [BoardController::class, 'show']);
    Route::post('/boards/{board}/contributions', [ContributionController::class, 'store']);
    Route::get('/stats', [StatsController::class, 'index']);
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
});
