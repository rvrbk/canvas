<?php

namespace App\Models;

use App\BoardStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    /** @use HasFactory<\Database\Factories\BoardFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'width',
        'height',
        'background_color',
        'max_contributions',
    ];

    protected function casts(): array
    {
        return [
            'status' => BoardStatus::class,
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<Contribution, $this>
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function isOpen(): bool
    {
        return $this->status === BoardStatus::Open;
    }

    public function hasContributionFrom(User $user): bool
    {
        return $this->contributions()->where('user_id', $user->id)->exists();
    }
}
