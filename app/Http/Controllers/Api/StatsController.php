<?php

namespace App\Http\Controllers\Api;

use App\BoardStatus;
use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * @var array<string, array{emoji: string, label: string, description: string, threshold: int, metric: string}>
     */
    private const BADGES = [
        'first_stroke' => [
            'emoji' => '🎨',
            'label' => 'First Stroke',
            'description' => 'Submit your first drawing',
            'threshold' => 1,
            'metric' => 'contributions',
        ],
        'getting_colorful' => [
            'emoji' => '🌈',
            'label' => 'Getting Colorful',
            'description' => 'Submit 5 drawings',
            'threshold' => 5,
            'metric' => 'contributions',
        ],
        'prolific_painter' => [
            'emoji' => '🖌️',
            'label' => 'Prolific Painter',
            'description' => 'Submit 15 drawings',
            'threshold' => 15,
            'metric' => 'contributions',
        ],
        'board_completer' => [
            'emoji' => '✅',
            'label' => 'Board Completer',
            'description' => 'Help complete a board',
            'threshold' => 1,
            'metric' => 'completed_boards',
        ],
    ];

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $contributionsCount = Contribution::query()->where('user_id', $userId)->count();

        $completedBoardsCount = Contribution::query()
            ->where('user_id', $userId)
            ->whereHas('board', fn ($query) => $query->where('status', BoardStatus::Completed))
            ->count();

        $points = ($contributionsCount * 10) + ($completedBoardsCount * 50);
        $level = intdiv($points, 100) + 1;
        $pointsIntoLevel = $points % 100;

        $metrics = [
            'contributions' => $contributionsCount,
            'completed_boards' => $completedBoardsCount,
        ];

        $badges = collect(self::BADGES)->map(function (array $badge, string $key) use ($metrics) {
            return [
                'key' => $key,
                'emoji' => $badge['emoji'],
                'label' => $badge['label'],
                'description' => $badge['description'],
                'achieved' => $metrics[$badge['metric']] >= $badge['threshold'],
            ];
        })->values();

        return response()->json([
            'data' => [
                'contributions_count' => $contributionsCount,
                'completed_boards_count' => $completedBoardsCount,
                'points' => $points,
                'level' => $level,
                'points_into_level' => $pointsIntoLevel,
                'points_to_next_level' => 100 - $pointsIntoLevel,
                'badges' => $badges,
            ],
        ]);
    }
}
