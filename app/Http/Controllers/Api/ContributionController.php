<?php

namespace App\Http\Controllers\Api;

use App\BoardStatus;
use App\Events\BoardCompleted;
use App\Events\ContributionSubmitted;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContributionRequest;
use App\Http\Resources\ContributionResource;
use App\Models\Board;
use App\Models\Contribution;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ContributionController extends Controller
{
    public function store(StoreContributionRequest $request, Board $board): JsonResponse
    {
        if (! $board->isOpen()) {
            return response()->json(['message' => 'This board is no longer accepting contributions.'], 422);
        }

        if ($board->hasContributionFrom($request->user())) {
            return response()->json(['message' => 'You have already contributed to this board.'], 422);
        }

        try {
            $contribution = DB::transaction(function () use ($request, $board): Contribution {
                $contribution = $board->contributions()->create([
                    'user_id' => $request->user()->id,
                    'strokes' => $request->validated('strokes'),
                    'layer_order' => $board->contributions()->count(),
                ]);

                if ($board->contributions()->count() >= $board->max_contributions) {
                    $board->status = BoardStatus::Completed;
                    $board->completed_at = now();
                    $board->save();
                }

                return $contribution;
            });
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return response()->json(['message' => 'You have already contributed to this board.'], 422);
            }

            throw $exception;
        }

        $board->refresh();

        if ($board->status === BoardStatus::Completed) {
            broadcast(new BoardCompleted($board));
            $this->startNextBoard($board);
        } else {
            broadcast(new ContributionSubmitted($board, $contribution));
        }

        return (new ContributionResource($contribution))
            ->response()
            ->setStatusCode(201);
    }

    private function startNextBoard(Board $completedBoard): void
    {
        Board::create([
            'name' => 'Collective Artwork #'.(Board::count() + 1),
            'width' => $completedBoard->width,
            'height' => $completedBoard->height,
            'background_color' => $completedBoard->background_color,
            'max_contributions' => $completedBoard->max_contributions,
        ]);
    }
}
