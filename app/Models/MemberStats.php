<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberStats extends Model
{
    protected $fillable = [
        'member_id',
        'team_id',
        'season_id',
        'division_id',
        'mvp_count',
        'fastest_501',
        'stats_calculated_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            // G-040: snake_case; G-001/G-031: nullable integer, no 255 sentinel
            'mvp_count'           => 'integer',
            'fastest_501'         => 'integer', // nullable — NULL = never finished
            'stats_calculated_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsTo<Member, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<Season, $this> */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /** @return BelongsTo<Division, $this> */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    /**
     * Scope to stats rows belonging to the current season(s).
     *
     * @param Builder<MemberStats> $query
     */
    public function scopeCurrentSeason(Builder $query): void
    {
        $query->whereHas('season', function (Builder $q): void {
            $q->where('status', 'current');
        });
    }

    /**
     * Scope to stats for a specific season.
     *
     * @param Builder<MemberStats> $query
     */
    public function scopeForSeason(Builder $query, int $seasonId): void
    {
        $query->where('season_id', $seasonId);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Whether this member has ever finished a 501 leg.
     * G-001/G-031: NULL means never finished (no 255 sentinel).
     */
    public function hasFinished501(): bool
    {
        return $this->fastest_501 !== null;
    }
}
