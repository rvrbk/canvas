<?php

namespace App\Events;

use App\Models\Board;
use App\Models\Contribution;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContributionSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Board $board, public Contribution $contribution)
    {
        //
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("board.{$this->board->id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'contribution.submitted';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'contributions_count' => $this->board->contributions()->count(),
            'max_contributions' => $this->board->max_contributions,
            'contribution' => [
                'id' => $this->contribution->id,
                'strokes' => $this->contribution->strokes,
                'layer_order' => $this->contribution->layer_order,
            ],
        ];
    }
}
