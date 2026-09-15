<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'width' => $this->width,
            'height' => $this->height,
            'background_color' => $this->background_color,
            'max_contributions' => $this->max_contributions,
            'status' => $this->status->value,
            'completed_at' => $this->completed_at,
            'contributions_count' => $this->whenCounted('contributions'),
            'has_contributed' => $request->user()
                ? $this->contributions()->where('user_id', $request->user()->id)->exists()
                : false,
            'contributions' => ContributionResource::collection($this->whenLoaded('contributions')),
        ];
    }
}
