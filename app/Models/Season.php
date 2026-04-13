<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = [
        'league_id',
        'year',
        'season_code',
        'week_count',
        'current_week',
        'status',
        'scoresheet_type',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'year'          => 'integer',
            'week_count'    => 'integer',
            'current_week'  => 'integer',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsTo<League, $this> */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /** @return HasMany<Division, $this> */
    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
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

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function isCurrent(): bool
    {
        return $this->status === 'current';
    }

    /** Human-readable label e.g. "Winter 2025" */
    public function label(): string
    {
        $map = ['sum' => 'Summer', 'fal' => 'Fall', 'win' => 'Winter'];

        return ($map[$this->season_code] ?? $this->season_code) . ' ' . $this->year;
    }
}
