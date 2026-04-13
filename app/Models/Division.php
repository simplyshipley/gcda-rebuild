<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $fillable = [
        'season_id',
        'code',
        'display_order',
        'is_twoofthree',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_twoofthree' => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsTo<Season, $this> */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /** @return HasMany<LeagueMatch, $this> */
    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class);
    }

    /** @return HasMany<MemberStats, $this> */
    public function memberStats(): HasMany
    {
        return $this->hasMany(MemberStats::class);
    }

    /** @return HasMany<HighScore, $this> */
    public function highScores(): HasMany
    {
        return $this->hasMany(HighScore::class);
    }
}
