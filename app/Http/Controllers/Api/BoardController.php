<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoardResource;
use App\Models\Board;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BoardController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $boards = Board::query()
            ->withCount('contributions')
            ->latest()
            ->paginate(12);

        return BoardResource::collection($boards);
    }

    public function show(Board $board): BoardResource
    {
        $board->loadCount('contributions');
        $board->load(['contributions' => fn ($query) => $query->orderBy('layer_order')]);

        return new BoardResource($board);
    }
}
