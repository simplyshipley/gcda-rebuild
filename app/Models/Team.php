<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'season_id',
        'division_id',
        'venue_id',
        'name',
        'captain_id',
        'starting_points',
        'penalties',
        'penalty_notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'starting_points' => 'integer',
            'penalties'       => 'integer',
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

    /** @return BelongsTo<Division, $this> */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /** @return BelongsTo<Venue, $this> */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /** @return BelongsTo<Member, $this> */
    public function captain(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'captain_id');
    }

    /** @return BelongsToMany<Member, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'team_members')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    /** @return HasMany<TeamMember, $this> */
    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    /** @return HasMany<LeagueMatch, $this> */
    public function homeMatches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'home_team_id');
    }

    /** @return HasMany<LeagueMatch, $this> */
    public function awayMatches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'away_team_id');
    }

    /** @return HasMany<MemberStats, $this> */
    public function memberStats(): HasMany
    {
        return $this->hasMany(MemberStats::class);
    }

    /** @return HasMany<Patch, $this> */
    public function patches(): HasMany
    {
        return $this->hasMany(Patch::class);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function effectivePoints(): int
    {
        return $this->starting_points - $this->penalties;
    }
}
