<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'dart_card_number',
        'first_name',
        'last_name',
        'nickname',
        'email',
        'photo_path',
        'is_substitute',
        'is_active',
        'is_placeholder',
        'placeholder_type',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_substitute'    => 'boolean',
            'is_active'        => 'boolean',
            'is_placeholder'   => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsToMany<Team, $this> */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    /** @return HasMany<TeamMember, $this> */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /** @return HasMany<MemberStats, $this> */
    public function stats(): HasMany
    {
        return $this->hasMany(MemberStats::class);
    }

    /** @return HasMany<Patch, $this> */
    public function patches(): HasMany
    {
        return $this->hasMany(Patch::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    /** @param Builder<Member> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<Member> $query */
    public function scopeSubstitutes(Builder $query): void
    {
        $query->where('is_substitute', true);
    }

    /** @param Builder<Member> $query */
    public function scopeRegular(Builder $query): void
    {
        $query->where('is_substitute', false)->where('is_placeholder', false);
    }

    // ──────────────────────────────────────────────
    // PPD Calculation Stub
    // ──────────────────────────────────────────────

    /**
     * Calculate Points Per Dart for this member.
     *
     * Formula: ((501 × finished_legs) - score_remaining) / total_darts
     *
     * Queries game_legs table across all matches for the given season.
     * Returns null if no 501 legs have been played.
     *
     * @todo Implement by querying GameLeg::whereIn('match_id', matchIds)
     *       for legs where this member appears as home_player1_id or away_player1_id
     *       in singles_501 or doubles_501 game_types.
     */
    public function calculatePpd(?int $seasonId = null): ?float
    {
        // Stub — to be implemented
        return null;
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function displayName(): string
    {
        if ($this->nickname) {
            return $this->nickname;
        }

        return $this->fullName();
    }
}
