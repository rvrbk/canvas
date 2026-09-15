<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()
            ->whereHas('contributions')
            ->withCount('contributions')
            ->orderByDesc('contributions_count')
            ->limit(10)
            ->get(['id', 'name']);

        $data = $users->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'contributions_count' => $user->contributions_count,
            'points' => $user->contributions_count * 10,
        ]);

        return response()->json(['data' => $data]);
    }
}
